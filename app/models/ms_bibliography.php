<?php
/** ---------------------------------------------------------------------
 * app/models/ms_bibliography.php
 * ----------------------------------------------------------------------
 * CollectiveAccess
 * Open-source collections management software
 * ----------------------------------------------------------------------
 *
 * Software by Whirl-i-Gig (http://www.whirl-i-gig.com)
 * Copyright 2011 Whirl-i-Gig
 *
 * For more information visit http://www.CollectiveAccess.org
 *
 * This program is free software; you may redistribute it and/or modify it under
 * the terms of the provided license as published by Whirl-i-Gig
 *
 * CollectiveAccess is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTIES whatsoever, including any implied warranty of 
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  
 *
 * This source code is free and modifiable under the terms of 
 * GNU General Public License. (http://www.gnu.org/copyleft/gpl.html). See
 * the "license.txt" file for details, or visit the CollectiveAccess web site at
 * http://www.CollectiveAccess.org
 * 
 * @package CollectiveAccess
 * @subpackage models
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License version 3
 * 
 * ----------------------------------------------------------------------
 */
 
require_once(__CA_LIB_DIR__."/core/BaseModel.php");

BaseModel::$s_ca_models_definitions['ms_bibliography'] = array(
 	'NAME_SINGULAR' 	=> _t('bibliographic citation'),
 	'NAME_PLURAL' 		=> _t('bibliographic citations'),
 	'FIELDS' 			=> array(
 		'bibref_id' => array(
				'FIELD_TYPE' => FT_NUMBER, 'DISPLAY_TYPE' => DT_HIDDEN, 
				'IDENTITY' => true, 'DISPLAY_WIDTH' => 10, 'DISPLAY_HEIGHT' => 1,
				'IS_NULL' => false, 
				'DEFAULT' => '',
				'LABEL' => _t('bibliographic reference id'), 'DESCRIPTION' => _t('Unique numeric identifier used to identify this bibliographic citation')
		),
		'project_id' => array(
				'FIELD_TYPE' => FT_NUMBER, 'DISPLAY_TYPE' => DT_HIDDEN,
				'DISPLAY_WIDTH' => 10, 'DISPLAY_HEIGHT' => 1,
				'IS_NULL' => false, 
				'DEFAULT' => '',
				'LABEL' => 'Project id', 'DESCRIPTION' => 'Project id'
		),
		'user_id' => array(
				'FIELD_TYPE' => FT_NUMBER, 'DISPLAY_TYPE' => DT_HIDDEN,
				'DISPLAY_WIDTH' => 10, 'DISPLAY_HEIGHT' => 1,
				'IS_NULL' => false, 
				'DEFAULT' => '',
				'LABEL' => 'Row id', 'DESCRIPTION' => 'Project administrator'
		),
		'reference_type' => array(
				'FIELD_TYPE' => FT_NUMBER, 'DISPLAY_TYPE' => DT_SELECT,
				'DISPLAY_WIDTH' => 100, 'DISPLAY_HEIGHT' => 3,
				'IS_NULL' => false,
				'DEFAULT' => '',
				'LABEL' => 'Reference type',
				'DESCRIPTION' => 'Choose the most specific reference type that is appropriate for the reference. If you are unsure, choose &quot;Generic&quot;.',
				'BOUNDS_CHOICE_LIST' => array(
					"Generic" => 0,
					"Journal Article" => 1,
					"Book" => 2,
					"Book Section" => 3,
					"Manuscript" => 4,
					"Edited Book" => 5,
					"Magazine Article" => 6,
					"Newspaper Article" => 7,
					"Conference Proceedings" => 8,
 					"Thesis" => 9,
					"Report" => 10,
					"Personal Communication" => 11,
					"Electronic Source" => 13,
					"Audiovisual Material" => 14,
					"Artwork" => 16,
					"Map" => 17
				)
		),
		'article_title' => array(
				'FIELD_TYPE' => FT_TEXT, 'DISPLAY_TYPE' => DT_FIELD, 
				'DISPLAY_WIDTH' => 32, 'DISPLAY_HEIGHT' => 1,
				'IS_NULL' => false, 
				'DEFAULT' => '',
				'LABEL' => _t('Article title'), 'DESCRIPTION' => _t('Article title.'),
				'BOUNDS_LENGTH' => array(1,65535)
		),
		'article_secondary_title' => array(
				'FIELD_TYPE' => FT_TEXT, 'DISPLAY_TYPE' => DT_FIELD, 
				'DISPLAY_WIDTH' => 32, 'DISPLAY_HEIGHT' => 1,
				'IS_NULL' => false, 
				'DEFAULT' => '',
				'LABEL' => _t('Article secondary title'), 'DESCRIPTION' => _t('Article secondary title.'),
				'BOUNDS_LENGTH' => array(0,65535)
		),
		'journal_title' => array(
				'FIELD_TYPE' => FT_TEXT, 'DISPLAY_TYPE' => DT_FIELD, 
				'DISPLAY_WIDTH' => 32, 'DISPLAY_HEIGHT' => 1,
				'IS_NULL' => false, 
				'DEFAULT' => '',
				'LABEL' => _t('Journal title'), 'DESCRIPTION' => _t('Journal title.'),
				'BOUNDS_LENGTH' => array(0,65535)
		),
		'monograph_title' => array(
				'FIELD_TYPE' => FT_TEXT, 'DISPLAY_TYPE' => DT_FIELD, 
				'DISPLAY_WIDTH' => 32, 'DISPLAY_HEIGHT' => 1,
				'IS_NULL' => false, 
				'DEFAULT' => '',
				'LABEL' => _t('Monograph title'), 'DESCRIPTION' => _t('Monograph title.'),
				'BOUNDS_LENGTH' => array(0,65535)
		),
		'authors' => array(
				'FIELD_TYPE' => FT_TEXT, 'DISPLAY_TYPE' => DT_FIELD, 
				'DISPLAY_WIDTH' => 69, 'DISPLAY_HEIGHT' => 1,
				'IS_NULL' => false, 
				'DEFAULT' => '',
				'LABEL' => _t('Authors'), 'DESCRIPTION' => _t('Article authors.'),
				'BOUNDS_LENGTH' => array(1,65535)
		),
		'secondary_authors' => array(
				'FIELD_TYPE' => FT_TEXT, 'DISPLAY_TYPE' => DT_FIELD, 
				'DISPLAY_WIDTH' => 32, 'DISPLAY_HEIGHT' => 1,
				'IS_NULL' => false, 
				'DEFAULT' => '',
				'LABEL' => _t('Secondary authors'), 'DESCRIPTION' => _t('Article secondary authors.'),
				'BOUNDS_LENGTH' => array(0,65535)
		),
		'editors' => array(
				'FIELD_TYPE' => FT_TEXT, 'DISPLAY_TYPE' => DT_FIELD, 
				'DISPLAY_WIDTH' => 32, 'DISPLAY_HEIGHT' => 1,
				'IS_NULL' => false, 
				'DEFAULT' => '',
				'LABEL' => _t('Editors'), 'DESCRIPTION' => _t('Article editors.'),
				'BOUNDS_LENGTH' => array(0,65535)
		),
		'author_address' => array(
				'FIELD_TYPE' => FT_TEXT, 'DISPLAY_TYPE' => DT_FIELD, 
				'DISPLAY_WIDTH' => 69, 'DISPLAY_HEIGHT' => 1,
				'IS_NULL' => false, 
				'DEFAULT' => '',
				'LABEL' => _t('Author address'), 'DESCRIPTION' => _t('Author address'),
				'BOUNDS_LENGTH' => array(0,65535)
		),
		'vol' => array(
				'FIELD_TYPE' => FT_TEXT, 'DISPLAY_TYPE' => DT_FIELD, 
				'DISPLAY_WIDTH' => 20, 'DISPLAY_HEIGHT' => 1,
				'IS_NULL' => false, 
				'DEFAULT' => '',
				'LABEL' => _t('Volume'), 'DESCRIPTION' => _t('Journal volume.'),
				'BOUNDS_LENGTH' => array(0,45)
		),
		'num' => array(
				'FIELD_TYPE' => FT_TEXT, 'DISPLAY_TYPE' => DT_FIELD, 
				'DISPLAY_WIDTH' => 20, 'DISPLAY_HEIGHT' => 1,
				'IS_NULL' => false, 
				'DEFAULT' => '',
				'LABEL' => _t('Number'), 'DESCRIPTION' => _t('Volume number.'),
				'BOUNDS_LENGTH' => array(0,45)
		),
		'edition' => array(
				'FIELD_TYPE' => FT_TEXT, 'DISPLAY_TYPE' => DT_FIELD, 
				'DISPLAY_WIDTH' => 20, 'DISPLAY_HEIGHT' => 1,
				'IS_NULL' => false, 
				'DEFAULT' => '',
				'LABEL' => _t('Edition'), 'DESCRIPTION' => _t('Edition.'),
				'BOUNDS_LENGTH' => array(0,100)
		),
		'pubyear' => array(
				'FIELD_TYPE' => FT_NUMBER, 'DISPLAY_TYPE' => DT_FIELD, 
				'DISPLAY_WIDTH' => 20, 'DISPLAY_HEIGHT' => 1,
				'IS_NULL' => false, 
				'DEFAULT' => '',
				'LABEL' => _t('Publication year'), 'DESCRIPTION' => _t('Journal year of publication.'),
				'BOUNDS_LENGTH' => array(0,4)
		),
		'publisher' => array(
				'FIELD_TYPE' => FT_TEXT, 'DISPLAY_TYPE' => DT_FIELD, 
				'DISPLAY_WIDTH' => 20, 'DISPLAY_HEIGHT' => 1,
				'IS_NULL' => false, 
				'DEFAULT' => '',
				'LABEL' => _t('Publisher'), 'DESCRIPTION' => _t('Journal publisher.'),
				'BOUNDS_LENGTH' => array(0,65535)
		),
		'place_of_publication' => array(
				'FIELD_TYPE' => FT_TEXT, 'DISPLAY_TYPE' => DT_FIELD, 
				'DISPLAY_WIDTH' => 20, 'DISPLAY_HEIGHT' => 1,
				'IS_NULL' => false, 
				'DEFAULT' => '',
				'LABEL' => _t('Place of publication'), 'DESCRIPTION' => _t('Place of journal publication.'),
				'BOUNDS_LENGTH' => array(0,65535)
		),
		'abstract' => array(
				'FIELD_TYPE' => FT_TEXT, 'DISPLAY_TYPE' => DT_FIELD, 
				'DISPLAY_WIDTH' => 68, 'DISPLAY_HEIGHT' => 3,
				'IS_NULL' => false, 
				'DEFAULT' => '',
				'LABEL' => _t('Abstract'), 'DESCRIPTION' => _t('Article abstract.'),
				'BOUNDS_LENGTH' => array(0,65535)
		),
		'description' => array(
				'FIELD_TYPE' => FT_TEXT, 'DISPLAY_TYPE' => DT_FIELD, 
				'DISPLAY_WIDTH' => 68, 'DISPLAY_HEIGHT' => 2,
				'IS_NULL' => false, 
				'DEFAULT' => '',
				'LABEL' => _t('Description'), 'DESCRIPTION' => _t('Article description.'),
				'BOUNDS_LENGTH' => array(0,65535)
		),
		'collation' => array(
				'FIELD_TYPE' => FT_TEXT, 'DISPLAY_TYPE' => DT_FIELD, 
				'DISPLAY_WIDTH' => 13, 'DISPLAY_HEIGHT' => 1,
				'IS_NULL' => false, 
				'DEFAULT' => '',
				'LABEL' => _t('Collation'), 'DESCRIPTION' => _t('Article collation.'),
				'BOUNDS_LENGTH' => array(0,255)
		),
		'sect' => array(
				'FIELD_TYPE' => FT_TEXT, 'DISPLAY_TYPE' => DT_FIELD, 
				'DISPLAY_WIDTH' => 13, 'DISPLAY_HEIGHT' => 1,
				'IS_NULL' => false, 
				'DEFAULT' => '',
				'LABEL' => _t('Sect'), 'DESCRIPTION' => _t('Sect.'),
				'BOUNDS_LENGTH' => array(0,100)
		),
		'worktype' => array(
				'FIELD_TYPE' => FT_TEXT, 'DISPLAY_TYPE' => DT_FIELD, 
				'DISPLAY_WIDTH' => 13, 'DISPLAY_HEIGHT' => 1,
				'IS_NULL' => false, 
				'DEFAULT' => '',
				'LABEL' => _t('Worktype'), 'DESCRIPTION' => _t('Worktype.'),
				'BOUNDS_LENGTH' => array(0,100)
		),
		'pp' => array(
				'FIELD_TYPE' => FT_TEXT, 'DISPLAY_TYPE' => DT_FIELD, 
				'DISPLAY_WIDTH' => 13, 'DISPLAY_HEIGHT' => 1,
				'IS_NULL' => false, 
				'DEFAULT' => '',
				'LABEL' => _t('Page number'), 'DESCRIPTION' => _t('Page number'),
				'BOUNDS_LENGTH' => array(0,45)
		),
		'isbn' => array(
				'FIELD_TYPE' => FT_TEXT, 'DISPLAY_TYPE' => DT_FIELD, 
				'DISPLAY_WIDTH' => 32, 'DISPLAY_HEIGHT' => 1,
				'IS_NULL' => false, 
				'DEFAULT' => '',
				'LABEL' => _t('ISBN'), 'DESCRIPTION' => _t('ISBN.'),
				'BOUNDS_LENGTH' => array(0,100)
		),
		'url' => array(
				'FIELD_TYPE' => FT_TEXT, 'DISPLAY_TYPE' => DT_FIELD, 
				'DISPLAY_WIDTH' => 32, 'DISPLAY_HEIGHT' => 1,
				'IS_NULL' => false, 
				'DEFAULT' => '',
				'LABEL' => _t('Article URL'), 'DESCRIPTION' => _t('Article URL.'),
				'BOUNDS_LENGTH' => array(0,65535)
		),
		'external_identifier' => array(
				'FIELD_TYPE' => FT_TEXT, 'DISPLAY_TYPE' => DT_FIELD, 
				'DISPLAY_WIDTH' => 32, 'DISPLAY_HEIGHT' => 1,
				'IS_NULL' => false, 
				'DEFAULT' => '',
				'LABEL' => _t('External identifier'), 'DESCRIPTION' => _t('External identifier.'),
				'BOUNDS_LENGTH' => array(0,65535)
		),
		'electronic_resource_num' => array(
				'FIELD_TYPE' => FT_TEXT, 'DISPLAY_TYPE' => DT_FIELD, 
				'DISPLAY_WIDTH' => 32, 'DISPLAY_HEIGHT' => 1,
				'IS_NULL' => false, 
				'DEFAULT' => '',
				'LABEL' => _t('Electronic resource number'), 'DESCRIPTION' => _t('Electronic resource number'),
				'BOUNDS_LENGTH' => array(0,255)
		),
		'language' => array(
				'FIELD_TYPE' => FT_TEXT, 'DISPLAY_TYPE' => DT_FIELD, 
				'DISPLAY_WIDTH' => 20, 'DISPLAY_HEIGHT' => 1,
				'IS_NULL' => false, 
				'DEFAULT' => '',
				'LABEL' => _t('Language'), 'DESCRIPTION' => _t('Language of article.'),
				'BOUNDS_LENGTH' => array(0,100)
		),
		'keywords' => array(
				'FIELD_TYPE' => FT_TEXT, 'DISPLAY_TYPE' => DT_FIELD, 
				'DISPLAY_WIDTH' => 44, 'DISPLAY_HEIGHT' => 1,
				'IS_NULL' => false, 
				'DEFAULT' => '',
				'LABEL' => _t('Keywords'), 'DESCRIPTION' => _t('Keywords.'),
				'BOUNDS_LENGTH' => array(0,100)
		),
		'created_on' => array(
				'FIELD_TYPE' => FT_TIMESTAMP, 'DISPLAY_TYPE' => DT_FIELD,
				'DISPLAY_WIDTH' => 10, 'DISPLAY_HEIGHT' => 1,
				'IS_NULL' => false, 
				'DEFAULT' => '',
				'LABEL' => _t('Created on'), 'DESCRIPTION' => _t('Date/time the bibliography was created.'),
		),
		'last_modified_on' => array(
				'FIELD_TYPE' => FT_TIMESTAMP, 'DISPLAY_TYPE' => DT_FIELD, 'UPDATE_ON_UPDATE' => true,
				'DISPLAY_WIDTH' => 10, 'DISPLAY_HEIGHT' => 1,
				'IS_NULL' => false, 
				'DEFAULT' => '',
				'LABEL' => _t('Last modified on'), 'DESCRIPTION' => _t('Date/time the bibliography was last modified.'),
		),
		'approval_status' => array(
				'FIELD_TYPE' => FT_NUMBER, 'DISPLAY_TYPE' => DT_HIDDEN, 
				'DISPLAY_WIDTH' => 40, 'DISPLAY_HEIGHT' => 1,
				'IS_NULL' => false, 
				'DEFAULT' => 0,
				'LABEL' => _t('Approval status'), 'DESCRIPTION' => _t('Approval status'),
				"BOUNDS_CHOICE_LIST"=> array(
					_t('New') 	=> 0,
					_t('Approved')	=> 1
				)
		)
 	)
);

class ms_bibliography extends BaseModel {
	# ---------------------------------
	# --- Object attribute properties
	# ---------------------------------
	# Describe structure of content object's properties - eg. database fields and their
	# associated types, what modes are supported, et al.
	#

	# ------------------------------------------------------
	# --- Basic object parameters
	# ------------------------------------------------------
	# what table does this class represent?
	protected $TABLE = 'ms_bibliography';
	      
	# what is the primary key of the table?
	protected $PRIMARY_KEY = 'bibref_id';

	# ------------------------------------------------------
	# --- Properties used by standard editing scripts
	# 
	# These class properties allow generic scripts to properly display
	# records from the table represented by this class
	#
	# ------------------------------------------------------

	# Array of fields to display in a listing of records from this table
	protected $LIST_FIELDS = array('authors', 'article_title');

	# When the list of "list fields" above contains more than one field,
	# the LIST_DELIMITER text is displayed between fields as a delimiter.
	# This is typically a comma or space, but can be any string you like
	protected $LIST_DELIMITER = ', ';


	# What you'd call a single record from this table (eg. a "person")
	protected $NAME_SINGULAR;

	# What you'd call more than one record from this table (eg. "people")
	protected $NAME_PLURAL;

	# List of fields to sort listing of records by; you can use 
	# SQL 'ASC' and 'DESC' here if you like.
	protected $ORDER_BY = array('authors');

	# If you want to order records arbitrarily, add a numeric field to the table and place
	# its name here. The generic list scripts can then use it to order table records.
	protected $RANK = '';
	
	# ------------------------------------------------------
	# Hierarchical table properties
	# ------------------------------------------------------
	protected $HIERARCHY_TYPE				=	null;
	protected $HIERARCHY_LEFT_INDEX_FLD 	= 	null;
	protected $HIERARCHY_RIGHT_INDEX_FLD 	= 	null;
	protected $HIERARCHY_PARENT_ID_FLD		=	null;
	protected $HIERARCHY_DEFINITION_TABLE	=	null;
	protected $HIERARCHY_ID_FLD				=	null;
	protected $HIERARCHY_POLY_TABLE			=	null;
	
	# ------------------------------------------------------
	# Change logging
	# ------------------------------------------------------
	protected $UNIT_ID_FIELD = null;
	protected $LOG_CHANGES_TO_SELF = false;
	protected $LOG_CHANGES_USING_AS_SUBJECT = array(
		"FOREIGN_KEYS" => array(
		
		),
		"RELATED_TABLES" => array(
		
		)
	);	
	
	
	# ------------------------------------------------------
	# $FIELDS contains information about each field in the table. The order in which the fields
	# are listed here is the order in which they will be returned using getFields()

	protected $FIELDS;
	
	# ----------------------------------------
	public function __construct($pn_id=null) {
		parent::__construct($pn_id);
	}
	# ----------------------------------------
	function getCitationText($pa_record=null) {
		$va_titles = array();
		if (is_array($pa_record)) {
			if(trim($pa_record['article_title'])) { $va_titles['article_title'] = trim($pa_record['article_title']); }
			if(trim($pa_record['article_secondary_title'])) { $va_titles['article_secondary_title'] = trim($pa_record['article_secondary_title']); }
			if(trim($pa_record['journal_title'])) { $va_titles['journal_title'] = trim($pa_record['journal_title']); }
			if(trim($pa_record['monograph_title'])) { $va_titles['monograph_title'] = trim($pa_record['monograph_title']); }
			
			$vn_reference_id = intval($pa_record['reference_id']);
			$vs_article_title = trim($pa_record['article_title']);
			$vs_journal_title = trim($pa_record['journal_title']);
			$vs_monograph_title = trim($pa_record['monograph_title']);
			
			$vs_authors = trim($pa_record['authors']);
			$vs_editors = trim($pa_record['editors']);
			$vs_secondary_authors = trim($pa_record['secondary_authors']);
			$vn_pubdate = trim($pa_record['pubyear']);
			$vs_publisher = trim($pa_record['publisher']);
			$vs_place_of_publication = trim($pa_record['place_of_publication']);
			$vn_volume = trim($pa_record['vol']);
			$vn_num = trim($pa_record['num']);
			$vs_section = trim($pa_record['sect']);
			$vs_edition = trim($pa_record['edition']);
			$vs_collation = trim($pa_record['collation']);
			$vs_reference_type = intval($pa_record['reference_type']);
			$vs_pp = trim($pa_record['pp']);
			
		} else {
			if ($vs_tmp = trim($this->get('article_title'))) { $va_titles['article_title'] = $vs_tmp; }
			if ($vs_tmp = trim($this->get('article_secondary_title'))) { $va_titles['article_secondary_title'] = $vs_tmp; }
			if ($vs_tmp = trim($this->get('journal_title'))) { $va_titles['journal_title'] = $vs_tmp; }
			if ($vs_tmp = trim($this->get('monograph_title'))) { $va_titles['monograph_title'] = $vs_tmp; }
			
			$vn_reference_id = intval($this->get('reference_id'));
			$vs_article_title = trim($this->get('article_title'));
			$vs_journal_title = trim($this->get('journal_title'));
			$vs_monograph_title = trim($this->get('monograph_title'));
		
			$vs_authors = trim($this->get('authors'));
			$vs_editors = trim($this->get('editors'));
			$vs_secondary_authors = trim($this->get('secondary_authors'));
			$vn_pubdate = trim($this->get('pubyear'));
			$vs_publisher = trim($this->get('publisher'));
			$vs_place_of_publication = trim($this->get('place_of_publication'));
			$vn_volume = trim($this->get('vol'));
			$vn_num = trim($this->get('num'));
			$vs_edition = trim($this->get('edition'));
			$vs_section = trim($this->get('sect'));
			$vs_collation = trim($this->get('collation'));
			$vs_reference_type = intval($this->get('reference_type'));
			$vs_pp = trim($this->get('pp'));

		}
		
		$vs_citation = '';
		if ($vs_authors) { $vs_citation = $vs_authors.((preg_match("/\.$/", $vs_authors)) ? ' ' : '. '); }
		if ($vs_secondary_authors) { $vs_citation .= $vs_secondary_authors.((preg_match("/\.$/", $vs_secondary_authors)) ? ' ' : '. '); }
		if ($vn_pubdate) { $vs_citation .= $vn_pubdate.'. '; }
		//if ($vs_article_title) { $vs_citation .= $vs_article_title.'. '; }
		//if ($vs_journal_title) { $vs_citation .= $vs_journal_title.'. '; }
		//if ($vs_monograph_title) { $vs_citation .= $vs_monograph_title.'. '; }
		foreach($va_titles as $vs_type=>$vs_title) {
			if($vs_type == 'journal_title')
				$vs_citation .= ($vs_reference_type == 5 ||  $vs_reference_type == 3 || $vs_reference_type == 2 ? 'In ' : '') ."<em>".  $vs_title."</em>";
			else
				$vs_citation .= $vs_title;
			if (!preg_match("/\.$/", $vs_title)) {
				$vs_citation .= '.';
			}
			$vs_citation .= ' ';
		}
		
		if ($vn_volume) { 
			$vs_citation .= 'Vol. '. $vn_volume;
		}
		if ($vn_num) { $vs_citation .= '('.$vn_num.')'; }

		if ($vs_collation) {
			$vs_citation .= ( $vn_volume ? ', ' : ' ').(preg_match('/[\-\,]/', $vs_collation) ? ' pp. ' : ' p. ') .$vs_collation;
		}
		
		if ($vs_editors) {
			$vs_citation .= ( $vs_collation ? ', ': ' in ').$vs_editors.' <i>ed</i>';
		}
		if ($vn_volume || $vn_num || $vs_collation || $vs_editors) {
			$vs_citation .= '. ';
		}
		if ($vs_section) { $vs_citation .= ' Section: '.$vs_section.'. '; }
		if ($vs_edition) { $vs_citation .= ' Edition: '.$vs_edition.'. '; }
		
		if ($vs_publisher) { $vs_citation .= $vs_publisher; }
		if ($vs_place_of_publication) { 
			if ($vs_publisher) {
				$vs_citation .= ',';
			}
			$vs_citation .= ' '.$vs_place_of_publication.'. '; 
		} else {
			if ($vs_publisher) {
				$vs_citation .= '. ';
			}
		}
		if($vs_pp){
			if(stristr($vs_pp, "p")){
				$vs_citation .= ' '.$vs_pp.'.';
			}else{
				if(stristr($vs_pp, "-")){
					$vs_citation .= ' pp. '.$vs_pp.'.';
				}else{
					$vs_citation .= ' p. '.$vs_pp.'.';
				}
			}
		}
		
		return $vs_citation;
	}
	# ----------------------------------------
}
?>