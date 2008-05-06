<?php
	$test[] = '0.9.14.001';
	function phpbrain_upgrade0_9_14_001()
	{
		global $DEBUG;

		$db1 = $GLOBALS['phpgw_setup']->db;

		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'phpgw_kb_ratings', array(
				'fd' => array(
					'user_id'	=> array('type' => 'int','precision' => '4', 'nullable' => False),
					'art_id'	=> array('type' => 'int','precision' => '4','nullable' => False)
				),
				'pk' => array('user_id', 'art_id'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array()
			)
		);

		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'phpgw_kb_related_art', array(
				'fd' => array(
					'art_id'			=> array('type' => 'int','precision' => '4','nullable' => False),
					'related_art_id'	=> array('type' => 'int','precision' => '4','nullable' => False)
				),
				'pk' => array('art_id', 'related_art_id'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array()
			)
		);

		$GLOBALS['phpgw_setup']->oProc->CreateTable(
			'phpgw_kb_search', array(
				'fd' => array(
					'keyword'	=> array('type' => 'varchar', 'precision' => '10','nullable' => False),
					'art_id'	=> array('type' => 'int','precision' => '4','nullable' => False),
					'score'		=> array('type' => 'int','precision' => '8','nullable' => False)
				),
				'pk' => array('keyword', 'art_id'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array()
			)
		);
		if ($DEBUG) echo '<br>tables_update: new tables created';

		$GLOBALS['phpgw_setup']->oProc->RenameTable('phpgw_kb_faq', 'phpgw_kb_articles');
		$GLOBALS['phpgw_setup']->oProc->RenameColumn('phpgw_kb_articles', 'faq_id', 'art_id');
		$GLOBALS['phpgw_setup']->oProc->RenameColumn('phpgw_kb_articles', 'url', 'urls');
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('phpgw_kb_articles', 'urls', array('type' => 'text', 'nullable' => False));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('phpgw_kb_articles', 'q_id', array('type' => 'int', 'precision' => 8, 'nullable' => False));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('phpgw_kb_articles', 'topic', array('type' => 'text', 'nullable' => False));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('phpgw_kb_articles', 'created', array('type' => 'int', 'precision' => '4', 'nullable' => True));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('phpgw_kb_articles', 'modified_user_id', array('type' => 'int', 'precision' => '4', 'nullable' => False));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('phpgw_kb_articles', 'votes_1', array('type' => 'int', 'precision' => '4', 'nullable' => False));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('phpgw_kb_articles', 'votes_2', array('type' => 'int', 'precision' => '4', 'nullable' => False));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('phpgw_kb_articles', 'votes_3', array('type' => 'int', 'precision' => '4', 'nullable' => False));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('phpgw_kb_articles', 'votes_4', array('type' => 'int', 'precision' => '4', 'nullable' => False));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('phpgw_kb_articles', 'votes_5', array('type' => 'int', 'precision' => '4', 'nullable' => False));
		$GLOBALS['phpgw_setup']->oProc->AddColumn('phpgw_kb_articles', 'files', array('type' => 'text', 'nullable' => False));
		if ($DEBUG) echo '<br>tables_update: added columns to phpgw_kb_articles';

		$sql = "SELECT art_id, user_id, modified, urls, votes, total FROM phpgw_kb_articles";
		$GLOBALS['phpgw_setup']->oProc->query($sql, __LINE__, __FILE__);
		if ($DEBUG) echo '<br>tables_update: query on phpgw_kb_articles executed';
		while($GLOBALS['phpgw_setup']->oProc->next_record())
		{
			$art_id		= $GLOBALS['phpgw_setup']->oProc->f('art_id');
			$user_id	= $GLOBALS['phpgw_setup']->oProc->f('user_id');
			$keywords	= $GLOBALS['phpgw_setup']->oProc->f('keywords');
			$modified	= $GLOBALS['phpgw_setup']->oProc->f('modified');
			$urls 		= $GLOBALS['phpgw_setup']->oProc->f('urls');
			$votes		= $GLOBALS['phpgw_setup']->oProc->f('votes');
			$total		= $GLOBALS['phpgw_setup']->oProc->f('total');

			if ($urls)
			{
				$new_urls = serialize(array(0=>array('link' => $urls, 'title' => '')));
			}
			else
			{
				$new_urls = '';
			}
			if (!$votes)
			{
				$average = -1;
			}
			else
			{
				$average = round($total / $votes);
			}
			$votes_str = array();
			for ($j=1; $j<=5; $j++)
			{
				if ($j == $average)
				{
					$votes_str[] = "votes_" . $j . "=$votes";
				}
				else
				{
					$votes_str[] = "votes_" . $j . "=0";
				}
			}
			$votes_str = implode(', ', $votes_str);
			$sql = "UPDATE phpgw_kb_articles SET created=$modified, modified_user_id=$user_id, $votes_str, urls='$new_urls', q_id='0', topic='', files=''  WHERE art_id=$art_id";
			$db1->query($sql, __LINE__, __FILE__);

			$sql = "INSERT INTO phpgw_kb_search (keyword, art_id, score) VALUES ('', $art_id, 1)";
			$db1->query($sql, __LINE__, __FILE__);
		}
		$new_table_def = array(
			'fd' => array(
				'art_id'			=> array('type' => 'auto','nullable' => False),
				'title'				=> array('type' => 'text','nullable' => False),
				'text'				=> array('type' => 'text','nullable' => False),
				'cat_id'			=> array('type' => 'int','precision' => '4','nullable' => False,'default' => '0'),
				'published' 		=> array('type' => 'int','precision' => '2','nullable' => False,'default' => '0'),
				'keywords'			=> array('type' => 'text','nullable' => False),
				'user_id'			=> array('type' => 'int','precision' => '4','nullable' => False,'default' => '0'),
				'views'				=> array('type' => 'int','precision' => '4','nullable' => False,'default' => '0'),
				'modified'			=> array('type' => 'int','precision' => '4','nullable' => True),
				'urls'				=> array('type' => 'text', 'nullable' => False),
				'votes'				=> array('type' => 'int','precision' => '4','nullable' => False,'default' => '0'),
				'total'				=> array('type' => 'int','precision' => '4','nullable' => False,'default' => '0'),
				'q_id'				=> array('type' => 'int', 'precision' => 8, 'nullable' => False),
				'topic'				=> array('type' => 'text', 'nullable' => False),
				'created'			=> array('type' => 'int','precision' => '4','nullable' => True),
				'modified_user_id'	=> array('type' => 'int','precision' => '4','nullable' => False),
				'votes_1'			=> array('type' => 'int','precision' => '4','nullable' => False),
				'votes_2'			=> array('type' => 'int','precision' => '4','nullable' => False),
				'votes_3'			=> array('type' => 'int','precision' => '4','nullable' => False),
				'votes_4'			=> array('type' => 'int','precision' => '4','nullable' => False),
				'votes_5'			=> array('type' => 'int','precision' => '4','nullable' => False),
				'files'				=> array('type' => 'text', 'nullable' => False)
			),
			'pk' => array('art_id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		);
		$GLOBALS['phpgw_setup']->oProc->DropColumn('phpgw_kb_articles', $new_table_def, 'is_faq');
		if ($DEBUG) echo '<br>tables_update: dropped column is_faq in phpgw_kb_articles';
		unset($new_table_def['fd']['votes']);
		$GLOBALS['phpgw_setup']->oProc->DropColumn('phpgw_kb_articles', $new_table_def, 'votes');
		if ($DEBUG) echo '<br>tables_update: dropped column votes in phpgw_kb_articles';
		unset($new_table_def['fd']['total']);
		$GLOBALS['phpgw_setup']->oProc->DropColumn('phpgw_kb_articles', $new_table_def, 'total');
		if ($DEBUG) echo '<br>tables_update: dropped column total in phpgw_kb_articles';

		$GLOBALS['phpgw_setup']->oProc->RenameColumn('phpgw_kb_comment', 'faq_id', 'art_id');
		if ($DEBUG) echo '<br>tables_update: renamed column faq_id to art_id in phpgw_kb_articles';
		$GLOBALS['phpgw_setup']->oProc->AddColumn('phpgw_kb_comment', 'published', array('type' => 'int', 'precision' => '2', 'nullable' => False));
		if ($DEBUG) echo '<br>tables_update: added column published in phpgw_kb_articles';
		$sql = "UPDATE phpgw_kb_comment SET published=1";
		$db1->query($sql, __LINE__, __FILE__);


		$GLOBALS['phpgw_setup']->oProc->AddColumn('phpgw_kb_questions', 'user_id', array('type' => 'int', 'precision' => '4', 'nullable' => False));
		if ($DEBUG) echo '<br>tables_update: added column user_id in phpgw_kb_questions';
		$GLOBALS['phpgw_setup']->oProc->AddColumn('phpgw_kb_questions', 'details', array('type' => 'text', 'nullable' => False));
		if ($DEBUG) echo '<br>tables_update: added column details in phpgw_kb_questions';
		$GLOBALS['phpgw_setup']->oProc->AddColumn('phpgw_kb_questions', 'cat_id', array('type' => 'int', 'precision' => '4', 'nullable' => False, 'default' => '0'));
		if ($DEBUG) echo '<br>tables_update: added column cat_id in phpgw_kb_questions';
		$GLOBALS['phpgw_setup']->oProc->AddColumn('phpgw_kb_questions', 'creation', array('type' => 'int', 'precision' => '4', 'nullable' => True));
		if ($DEBUG) echo '<br>tables_update: added column creation in phpgw_kb_questions';

		$sql = "SELECT question_id, pending FROM phpgw_kb_questions";
		$GLOBALS['phpgw_setup']->oProc->query($sql, __LINE__, __FILE__);
		while($GLOBALS['phpgw_setup']->oProc->next_record())
		{
			$question_id	= $GLOBALS['phpgw_setup']->oProc->f('question_id');
			$published		= $GLOBALS['phpgw_setup']->oProc->f('pending');
			$published = $published? 0 : 1;
			$sql = "UPDATE phpgw_kb_questions SET pending=$published, user_id='0', details='', cat_id='0', creation='". time() ."' WHERE question_id=$question_id";
			$db1->query($sql, __LINE__, __FILE__);
		}
		$GLOBALS['phpgw_setup']->oProc->RenameColumn('phpgw_kb_questions', 'question', 'summary');
		if ($DEBUG) echo '<br>tables_update: renamed column question to summary in phpgw_kb_questions';
		$GLOBALS['phpgw_setup']->oProc->RenameColumn('phpgw_kb_questions', 'pending', 'published');
		if ($DEBUG) echo '<br>tables_update: renamed column pending to published in phpgw_kb_questions';

		$GLOBALS['setup_info']['phpbrain']['currentver'] = '0.9.17.500';
		return $GLOBALS['setup_info']['phpbrain']['currentver'];
	}

	$test[] = '0.9.17.500';
	function phpbrain_upgrade0_9_17_500()
	{
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('phpgw_kb_search', 'keyword', array('type' => 'varchar', 'precision' => '30', 'nullable' => False));

		$GLOBALS['setup_info']['phpbrain']['currentver'] = '0.9.17.501';
		return $GLOBALS['setup_info']['phpbrain']['currentver'];
	}

	$test[] = '0.9.17.501';
	function phpbrain_upgrade0_9_17_501()
	{
		$db1 = $GLOBALS['phpgw_setup']->db;

		$sql = "SELECT art_id, text FROM phpgw_kb_articles";
		$GLOBALS['phpgw_setup']->oProc->query($sql, __LINE__, __FILE__);
		while($GLOBALS['phpgw_setup']->oProc->next_record())
		{
			$art_id = $GLOBALS['phpgw_setup']->oProc->f('art_id');
			$text = $GLOBALS['phpgw_setup']->oProc->f('text');
			
			if (!ereg("<[^<]+>.+<[^/]*/.+>", $text))
			{
				// text doesn't have html -> proceed to replace all \n by <br>
				$new_text = nl2br($text);

				$sql ="UPDATE phpgw_kb_articles SET text='$new_text' WHERE art_id = $art_id";
				$db1->query($sql, __LINE__, __FILE__);
			}
		}

		$GLOBALS['setup_info']['phpbrain']['currentver'] = '0.9.17.502';
		return $GLOBALS['setup_info']['phpbrain']['currentver'];
	}

	$test[] = '0.9.17.502';
	function phpbrain_upgrade0_9_17_502()
	{
		$GLOBALS['phpgw_setup']->oProc->CreateTable('phpgw_kb_files',array(
			'fd' => array(
				'art_id' => array('type' => 'int','precision' => '4'),
				'art_file' => array('type' => 'varchar','precision' => '255'),
				'art_file_comments' => array('type' => 'varchar','precision' => '255'),
			),
			'pk' => array('art_id','art_file'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		));

		$db2 = $GLOBALS['phpgw_setup']->db;
		$GLOBALS['phpgw_setup']->oProc->query("SELECT art_id,files FROM phpgw_kb_articles WHERE files != ''",__LINE__,__FILE__);
		while ($GLOBALS['phpgw_setup']->oProc->next_record())
		{
			$art_id = $GLOBALS['phpgw_setup']->oProc->f('art_id');
			$files = unserialize($GLOBALS['phpgw_setup']->oProc->f('files'));
			if (is_array($files))
			{
				foreach($files as $file)
				{
					$db2->insert('phpgw_kb_files',array(
						'art_id' => $art_id,
						'art_file'   => $file['file'],
						'art_file_comments'	=> $file['comment'],
					),false,__LINE__,__FILE__,'phpbrain');
				}
			}
		}
		
		$GLOBALS['setup_info']['phpbrain']['currentver'] = '0.9.17.503';
		return $GLOBALS['setup_info']['phpbrain']['currentver'];
	}


	$test[] = '0.9.17.503';
	function phpbrain_upgrade0_9_17_503()
	{
		$GLOBALS['phpgw_setup']->oProc->CreateTable('phpgw_kb_urls',array(
			'fd' => array(
				'art_id' => array('type' => 'int','precision' => '4'),
				'art_url' => array('type' => 'varchar','precision' => '255'),
				'art_url_title' => array('type' => 'varchar','precision' => '255')
			),
			'pk' => array('art_id','art_url'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		));

		$db2 = $GLOBALS['phpgw_setup']->db;
		$GLOBALS['phpgw_setup']->oProc->query("SELECT art_id,urls FROM phpgw_kb_articles WHERE urls != ''",__LINE__,__FILE__);
		while ($GLOBALS['phpgw_setup']->oProc->next_record())
		{
			$art_id = $GLOBALS['phpgw_setup']->oProc->f('art_id');
			$urls = unserialize($GLOBALS['phpgw_setup']->oProc->f('urls'));
			if (is_array($files))
			{
				foreach($urls as $url)
				{
					$db2->insert('phpgw_kb_files',array(
						'art_id' => $art_id,
						'art_url'    => $url['link'],
						'art_url_title'	=> $url['title'],
					),false,__LINE__,__FILE__,'phpbrain');
				}
			}
		}
		
		$GLOBALS['setup_info']['phpbrain']['currentver'] = '0.9.17.504';
		return $GLOBALS['setup_info']['phpbrain']['currentver'];
	}
	
	$test[] = '0.9.17.504';
	function phpbrain_upgrade0_9_17_504()
	{
		$GLOBALS['phpgw_setup']->oProc->DropColumn('phpgw_kb_articles',array(
			'fd' => array(
				'art_id' => array('type' => 'auto','nullable' => False),
				'q_id' => array('type' => 'int','precision' => '8','nullable' => False),
				'title' => array('type' => 'text','nullable' => False),
				'topic' => array('type' => 'text','nullable' => False),
				'text' => array('type' => 'text','nullable' => False),
				'cat_id' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '0'),
				'published' => array('type' => 'int','precision' => '2','nullable' => False,'default' => '0'),
				'user_id' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '0'),
				'views' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '0'),
				'created' => array('type' => 'int','precision' => '4','nullable' => True),
				'modified' => array('type' => 'int','precision' => '4','nullable' => True),
				'modified_user_id' => array('type' => 'int','precision' => '4','nullable' => False),
				'files' => array('type' => 'text','nullable' => False),
				'urls' => array('type' => 'text','nullable' => False),
				'votes_1' => array('type' => 'int','precision' => '4','nullable' => False),
				'votes_2' => array('type' => 'int','precision' => '4','nullable' => False),
				'votes_3' => array('type' => 'int','precision' => '4','nullable' => False),
				'votes_4' => array('type' => 'int','precision' => '4','nullable' => False),
				'votes_5' => array('type' => 'int','precision' => '4','nullable' => False)
			),
			'pk' => array('art_id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),'keywords');
		$GLOBALS['phpgw_setup']->oProc->DropColumn('phpgw_kb_articles',array(
			'fd' => array(
				'art_id' => array('type' => 'auto','nullable' => False),
				'q_id' => array('type' => 'int','precision' => '8','nullable' => False),
				'title' => array('type' => 'text','nullable' => False),
				'topic' => array('type' => 'text','nullable' => False),
				'text' => array('type' => 'text','nullable' => False),
				'cat_id' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '0'),
				'published' => array('type' => 'int','precision' => '2','nullable' => False,'default' => '0'),
				'user_id' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '0'),
				'views' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '0'),
				'created' => array('type' => 'int','precision' => '4','nullable' => True),
				'modified' => array('type' => 'int','precision' => '4','nullable' => True),
				'modified_user_id' => array('type' => 'int','precision' => '4','nullable' => False),
				'urls' => array('type' => 'text','nullable' => False),
				'votes_1' => array('type' => 'int','precision' => '4','nullable' => False),
				'votes_2' => array('type' => 'int','precision' => '4','nullable' => False),
				'votes_3' => array('type' => 'int','precision' => '4','nullable' => False),
				'votes_4' => array('type' => 'int','precision' => '4','nullable' => False),
				'votes_5' => array('type' => 'int','precision' => '4','nullable' => False)
			),
			'pk' => array('art_id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),'files');
		$GLOBALS['phpgw_setup']->oProc->DropColumn('phpgw_kb_articles',array(
			'fd' => array(
				'art_id' => array('type' => 'auto','nullable' => False),
				'q_id' => array('type' => 'int','precision' => '8','nullable' => False),
				'title' => array('type' => 'text','nullable' => False),
				'topic' => array('type' => 'text','nullable' => False),
				'text' => array('type' => 'text','nullable' => False),
				'cat_id' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '0'),
				'published' => array('type' => 'int','precision' => '2','nullable' => False,'default' => '0'),
				'user_id' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '0'),
				'views' => array('type' => 'int','precision' => '4','nullable' => False,'default' => '0'),
				'created' => array('type' => 'int','precision' => '4','nullable' => True),
				'modified' => array('type' => 'int','precision' => '4','nullable' => True),
				'modified_user_id' => array('type' => 'int','precision' => '4','nullable' => False),
				'votes_1' => array('type' => 'int','precision' => '4','nullable' => False),
				'votes_2' => array('type' => 'int','precision' => '4','nullable' => False),
				'votes_3' => array('type' => 'int','precision' => '4','nullable' => False),
				'votes_4' => array('type' => 'int','precision' => '4','nullable' => False),
				'votes_5' => array('type' => 'int','precision' => '4','nullable' => False)
			),
			'pk' => array('art_id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),'urls');
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('phpgw_kb_articles','title',array(
			'type' => 'varchar',
			'precision' => '255',
			'nullable' => False
		));
		$GLOBALS['phpgw_setup']->oProc->AlterColumn('phpgw_kb_articles','topic',array(
			'type' => 'varchar',
			'precision' => '255',
			'nullable' => False
		));

		$GLOBALS['setup_info']['phpbrain']['currentver'] = '0.9.17.505';
		return $GLOBALS['setup_info']['phpbrain']['currentver'];
	}
?>
