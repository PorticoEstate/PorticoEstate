<?php

namespace Smartsheet\Resources;

use Exception;
use Smartsheet\SmartsheetClient;
use Illuminate\Support\Collection;

class Sheet extends Resource
{
    protected SmartsheetClient $client;

    protected string $id;
    protected string $name;
    protected string $version;
    protected bool $hasSummaryFields;
    protected string $permalink;
    protected string $createdAt;
    protected string $modifiedAt;
    protected bool $isMultiPickListEnabled;
    protected array $columns;
    protected array $rows;

    public function __construct(SmartsheetClient $client, array $data)
    {
        parent::__construct($data);

        $this->client = $client;
    }

    public function dropAndReplace(array $rows)
    {
        $this->dropAllRows();

        foreach (collect($rows)->chunk(500) as $chunk) {
            $this->addRows($chunk->toArray());
        }
    }

    public function dropAllRows()
    {
        foreach (collect($this->get('rows'))->chunk(400) as $chunk) {
            $this->deleteRows(
                $chunk
                    ->pluck('id')
                    ->toArray()
            );
        }
    }

    public function dropAllColumnsExcept(array $columnNames)
    {
        $columnsToDelete = collect($this->columns)->filter(function ($column) use ($columnNames) {
            return !in_array($column->title, $columnNames);
        })->pluck('id');

        foreach ($columnsToDelete as $columnId) {
            $this->client->delete("sheets/$this->id/columns/$columnId");
            sleep(1);
        }
    }

    public function copyTo(string $sheetName, string $destinationFolderId)
    {
        return $this->client->post("sheets/$this->id/copy", [
            "json" => [
                'newName' => $sheetName,
                'destinationType' => 'folder',
                'destinationId' => $destinationFolderId
            ]
        ]);
    }

    public function copyRowsTo(array $rowIds, string $sheetId)
    {
        return $this->client->post("sheets/$this->id/rows/copy?include=all", [
            "json" => [
                'rowIds' => $rowIds,
                'to' => [
                    'sheetId' => $sheetId
                ]
            ]
        ]);
    }

    public function getRows(): Collection
    {
        return collect($this->rows)
            ->map(function ($row) {
                return new Row($this->client, (array) $row, $this);
            });
    }

    /**
     * @param $title
     * @return string
     * @throws Exception
     */
    public function getColumnId($title): string
    {
        $column = collect($this->columns)
            ->first(function ($col) use ($title) {
                return $col->title == $title;
            });

        if (is_null($column)) {
            throw new Exception('Unable to find column with the name: ' . $title);
        }

        return $column->id;
    }

    /**
     * @param array $cells
     * @return array
     * @throws Exception
     */
    protected function generateRowCells(array $cells): array
    {
        $newCells = [];

        foreach ($cells as $title => $value) {
            if (is_array($value)) {
                if (key_exists('formula', $value)) {
                    $newCells[] = [
                        'columnId' => $this->getColumnId($title),
                        'formula' => $value['formula']
                    ];
                } else {
                    $newCells[] = [
                        'columnId' => $this->getColumnId($title),
                        'objectValue' => $value
                    ];
                }
            } else {
                $newCells[] = [
                    'columnId' => $this->getColumnId($title),
                    'value' => $value
                ];
            }
        }

        return $newCells;
    }

    /**
     * Adds a row to the sheet
     *
     * @param array $rows
     * @return object
     */
    protected function insertRows(array $rows): object
    {
        return $this->client->post("sheets/$this->id/rows", [
            'json' => $rows
        ]);
    }


    /**
     * Adds a row to the sheet
     *
     * @param array $cells
     * @return object
     * @throws Exception
     */
    public function addRow(array $cells): object
    {
        return $this->insertRows([
            'toBottom' => true,
            'cells' => $this->generateRowCells($cells)
        ]);
    }

    /**
     * Adds a row to the sheet
     *
     * @param array $rows
     * @return object
     */
    public function addRows(array $rows): object
    {
        return $this->insertRows(
            collect($rows)
                ->map(function ($cells) {
                    return [
                        'toBottom' => true,
                        'cells' => $this->generateRowCells($cells)
                    ];
                })
                ->values()
                ->toArray()
        );
    }

    /**
     * @param array $rows
     * @throws Exception
     */
    public function updateRows(array $rows)
    {
        $rowsToUpdate = [];

        foreach ($rows as $id => $row) {
            $rowsToUpdate[] = [
                'id' => $id,
                'cells' => $this->generateRowCells($row)
            ];
        }

        return $this->client->put("sheets/$this->id/rows", [
            'json' => $rowsToUpdate
        ]);
    }

    /**
     * @param $rowId
     * @param array $cells
     * @return mixed
     * @throws Exception
     */
    public function updateRow($rowId, array $cells): mixed
    {
        $rowsToUpdate[] = [
            'id' => $rowId,
            'cells' => $this->generateRowCells($cells)
        ];

        return $this->client->put("sheets/$this->id/rows", [
            'json' => $rowsToUpdate
        ]);
    }

    public function replaceFirstRow(array $cells)
    {
        if (count($this->rows) > 0) {
            $this->updateRow($this->rows[0]->id, $cells);
        } else {
            $this->addRows([$cells]);
        }
    }

    public function sync(array $rows, string $primaryColumnName = 'primary')
    {
        $this->replaceRows($rows, $primaryColumnName);
    }

    public function replaceRows(array $cells, string $primaryColumnName)
    {
        if (count($this->rows) > 0) {
            $rowsToUpdate = [];

            foreach ($cells as $cell) {
                foreach ($this->getRows() as $row) {
                    if ($row->getCell($primaryColumnName) == $cell[$primaryColumnName]) {
                        $id = $row->getId();
                    }
                }

                if (isset($id)) {
                    $rowsToUpdate[$id] = $cell;
                }
            }

            $this->updateRows($rowsToUpdate);
        } else {

            $this->dropAllRows();

            $this->addRows([$cells]);
        }
    }

    /**
     * Adds a row to the sheet
     *
     * @param array $cells
     * @return object
     * @throws Exception
     */
    public function createRow(array $cells): object
    {
        return $this->insertRows([
            'toBottom' => true,
            'cells' => $this->generateRowCells($cells)
        ]);
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function rename(string $newName)
    {
        return $this->client->put("sheets/$this->id", [
            'json' => [
                'name' => $newName
            ]
        ]);
    }

    public function getShares()
    {
        return $this->client->get("sheets/$this->id/shares")->data;
    }

    public function shareSheet(array $shares)
    {
        return $this->client->post("sheets/$this->id/shares", [
            'json' => [...$shares]
        ]);
    }

    public function deleteRow(string $rowId)
    {
        return $this->deleteRows([$rowId]);
    }

    public function deleteRows(array $rowIds)
    {
        return $this->client->delete("sheets/$this->id/rows?ids=" . implode(',', $rowIds));
    }

    /**
     * @return array
     */
    public function getColumns(): array
    {
        return $this->columns;
    }

    public function addColumn(array $column)
    {
        return $this->addColumns([$column]);
    }

    public function addColumns(array $columns)
    {
        return $this->client->post("sheets/$this->id/columns", [
            'json' => $columns
        ]);
    }

    public function addSummaryField(String $title, String $formula, String $type = 'TEXT_NUMBER')
    {
        $options = [
                [
                'title' => $title,
                'type' => $type,
                'formula' => $formula
            ]
        ];
        return $this->client->post("sheets/$this->id/summary/fields", 
            ['json' => [...$options]]
        );
    }

    public function updateSummaryFieldByName(String $fieldName, array $summaryFieldDefinition)
    {
        $summaryField = $this->getSummaryFieldByName($fieldName);
        $summaryFieldDefinition['id'] = $summaryField->id;
        
        return $this->updateSummaryField($summaryFieldDefinition);
    }

    public function updateSummaryField(array $summaryField)
    {
        return $this->updateSummaryFields([$summaryField]);
    }

    public function updateSummaryFields(array $summaryFields)
    {
        return $this->client->put("sheets/$this->id/summary/fields", 
            ['json' => [...$summaryFields]]
        );
    }

    public function getSummaryFieldByName(String $fieldName)
    {
        return collect($this->getSummaryFields()->fields)
            ->first(fn ($field) => $field->title == $fieldName);
    }

    public function getSummaryFields()
    {
        return $this->client->get("sheets/$this->id/summary");
    }

    public function deleteSummaryFields(array $fieldIds)
    {
        return $this->client->delete("sheets/$this->id/summary/fields?ids=". implode(',', $fieldIds));
    }

    public function deleteSummaryField(String $fieldId)
    {
        return $this->deleteSummaryFields([$fieldId]);
    }

    public function deleteAllSummaryFields()
    {
        return $this->deleteSummaryFields(
            collect($this->getSummaryFields()->fields)
                ->pluck('id')
                ->toArray()
        );
    }
}
