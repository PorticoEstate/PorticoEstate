import java.sql.Connection;
import java.sql.SQLException;
import java.util.HashMap;

import net.sf.jasperreports.engine.JREmptyDataSource;
import net.sf.jasperreports.engine.JRException;
import net.sf.jasperreports.engine.JasperCompileManager;
import net.sf.jasperreports.engine.JasperFillManager;
import net.sf.jasperreports.engine.JasperPrint;
import net.sf.jasperreports.engine.JasperReport;
import net.sf.jasperreports.engine.export.HtmlExporter;
import net.sf.jasperreports.engine.export.JRCsvExporter;
import net.sf.jasperreports.engine.export.JRPdfExporter;
import net.sf.jasperreports.engine.export.JRXlsExporter;
import net.sf.jasperreports.engine.export.ooxml.JRDocxExporter;
import net.sf.jasperreports.engine.export.ooxml.JRXlsxExporter;
import net.sf.jasperreports.export.SimpleExporterInput;
import net.sf.jasperreports.export.SimpleHtmlExporterOutput;
import net.sf.jasperreports.export.SimpleOutputStreamExporterOutput;
import net.sf.jasperreports.export.SimpleWriterExporterOutput;
import net.sf.jasperreports.export.SimpleXlsReportConfiguration;
import net.sf.jasperreports.export.SimpleXlsxReportConfiguration;

class CustomJasperReport {

	private String name;
	private String source;

	private JasperPrint jasperPrint;

	public CustomJasperReport(String name, String source) {
		this.name = name;
		this.source = source;
		this.jasperPrint = null;

	}

	public void generateReport(HashMap<String, Object> parameters, JasperConnection jc) {

		JasperReport jasperReport = null;

		try {
			jasperReport = JasperCompileManager.compileReport(this.source);
		} catch (Exception e) {
//			System.out.println("Unable to compile template \"" + this.source
//					+ "\": " + e.getMessage());
			System.exit(201);
		}

		Connection connection = jc.makeConnection();

		try {

			if (connection != null) {
				this.jasperPrint = JasperFillManager.fillReport(
					jasperReport,
					parameters, 
					connection
				);
			} else {
				this.jasperPrint = JasperFillManager.fillReport(
					jasperReport,
						null,
					 new JREmptyDataSource(50)
				 );
			}

		} catch (JRException e1) {
//			System.out.println("Unable to fill the report for template \""
//					+ this.source + "\": " + e1.getMessage());
			System.exit(202);
		}

		try {
			if (connection != null) {
				connection.close();
			}
		} catch (SQLException e) {
			System.err.println("Unable to close connection");
			e.printStackTrace();
		}

	}

	public void generatePdf() {

		if (this.jasperPrint == null){
			System.exit(203);
		}

		JRPdfExporter pdfexp = new JRPdfExporter();
		pdfexp.setExporterInput(new SimpleExporterInput(this.jasperPrint));
		pdfexp.setExporterOutput(new SimpleOutputStreamExporterOutput(System.out));
		
		try {
			pdfexp.exportReport();
		} catch (JRException e) {
//			System.err.println("Unable to generate PDF file for report: "
//					+ this.name);
			System.exit(204);
		}

	}

	public void generateCSV() {

		if (this.jasperPrint == null) {
			System.exit(203);
		}

		JRCsvExporter csvexp = new JRCsvExporter();
		csvexp.setExporterInput(new SimpleExporterInput(this.jasperPrint));
		csvexp.setExporterOutput(new SimpleWriterExporterOutput(System.out));
		
		
		try {
			csvexp.exportReport();
		} catch (JRException e) {
//			System.err.println("Unable to generate CSV file for report: "
//					+ this.name);
			System.exit(205);
		}

	}

	public void generateJRXls() {
		if (this.jasperPrint == null) {
			System.exit(203);
		}

		JRXlsExporter jrxls = new JRXlsExporter();

		jrxls.setExporterInput(new SimpleExporterInput(this.jasperPrint));
		jrxls.setExporterOutput(new SimpleOutputStreamExporterOutput(System.out));
		SimpleXlsReportConfiguration configuration = new SimpleXlsReportConfiguration();
		configuration.setOnePagePerSheet(true);
		configuration.setDetectCellType(true);
		configuration.setCollapseRowSpan(false);
//		configuration.isRemoveEmptySpaceBetweenRows(true);
		jrxls.setConfiguration(configuration);

		try {
			jrxls.exportReport();
		} catch (JRException e) {
//			System.err.println("Unable to generate XLS file for report: "
//					+ this.name + ":" + e.getMessage());
			System.exit(206);
		}

	}

	public void generateJExcel() {
		if (this.jasperPrint == null) {
			System.exit(203);
		}

		JRXlsxExporter jexcel = new JRXlsxExporter();
		
		jexcel.setExporterInput(new SimpleExporterInput(this.jasperPrint));
		jexcel.setExporterOutput(new SimpleOutputStreamExporterOutput(System.out));
		SimpleXlsxReportConfiguration configuration = new SimpleXlsxReportConfiguration();
		configuration.setOnePagePerSheet(true);
		configuration.setDetectCellType(true);
		configuration.setCollapseRowSpan(false);
		jexcel.setConfiguration(configuration);

		
		try{
			jexcel.exportReport();
		} catch (JRException e) {
//			System.err.println("Unable to generate Excel XLS file for report: "
//					+ this.name + ":" + e.getMessage());
			System.exit(206);
		}
	}

	public void generateXhtml() {

		if (this.jasperPrint == null){
			System.exit(203);
		}

		HtmlExporter xhtmlexp = new HtmlExporter();

		xhtmlexp.setExporterInput(new SimpleExporterInput(this.jasperPrint));
		xhtmlexp.setExporterOutput(new SimpleHtmlExporterOutput(System.out));
			
		try {
			xhtmlexp.exportReport();
		} catch (JRException e) {
//			System.err.println("Unable to generate XHTML file for report: "
//					+ this.name);
			System.exit(218);
		}
	}

	public void generateDocx() {

		if (this.jasperPrint == null){
			System.exit(203);
		}

		JRDocxExporter docxexp = new JRDocxExporter();
		docxexp.setExporterInput(new SimpleExporterInput(this.jasperPrint));
		docxexp.setExporterOutput(new SimpleOutputStreamExporterOutput(System.out));
		
		try {
			docxexp.exportReport();
		} catch (JRException e) {
//			System.err.println("Unable to generate DOCX file for report: "
//					+ this.name);
			System.exit(219);
		}
	}


	public String getName() {
		return this.name;
	}

}
