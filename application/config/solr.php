<?php
/**
 * solr.php
 *
 * @author Simon Emms <simon@bigeyedeers.co.uk>
 */

/* Basic connection details */
$config['solr_hostname'] = 'localhost';
$config['solr_port'] = '8080';
$config['solr_path'] = '/solr';

/* Other config */
$config['solr_config'] = array(
    'show_errors' => true,          // Do we exit on errors?
	'table' => 'type',				// The column that is the "table" name
);

/*
 The “table” is also important.  Solr does not have tables like MySQL does.  What you have to do, when uploading the schema, is to add the “table” name to the schema and give it a name.  Then we are able to select the “table” in Solr.

Simple Select Query
The basic syntax is identical to the CodeIgniter Active Record class.

$this->solr->select()
->from('table_name')
->where('col_name', 'value')
->order_by('order', 'ASC')
->limit(10);
 
$arrSolr = $this->solr->qAssoc();
This produces the MySQL equivalent of 

SELECT * FROM `table_name` WHERE `col_name` = ‘value’ LIMIT 10;
Let’s go through this chained query in detail.

$this->solr->select()
Opens up the select statement.  You can specify “column” names.  

$this->solr->select('col_name');

->from('table_name')
Does exactly what it says on the tin.

->where('col_name', 'value')
A simple where statement.  By default, Solr uses the “OR” operator.

->where('col_name', 'value')
->where('other_col_name', 'other_value')
This would be the same as MySQL 

WHERE `col_name` = ‘value’ OR `other_col_name` = ‘other_value’; 
You can add ’=’ or ‘!=’ after the column name to give AND declarations.


->where('col_name =', 'value')
->where('other_col_name !=', 'other_value)
This now means 
WHERE `col_name` = ‘value’ AND `other_col_name` != ‘other_value’;

->order_by('order', 'ASC');
Same as the MySQL equivalent.  One of the best things about Solr is that, by default, it will generate the relevancy of the terms submitted to what’s in it’s schema.  If you don’t specify an order_by(), it will return it in relevancy order.  As Solr is mostly designed to be used as a search engine, this is usually the order you want to return it in anyway.

->limit(10);
Exactly the same as the MySQL one.  That means it only returns the first 10 rows.  NB.Because of the way Solr works, it will ALWAYS limit it’s results.  Fortunately, you’ve also got the offset command….

->limit(10, 20);
Then we execute
$arrSolr = $this->solr->qAssoc();
This executes the Solr statement and returns a mutli-level associative array.  If there is nothing to return, it will return false.  You will get returned everything you selected in theselect() command and a node with the ‘score’.

Array
(
    [0] => Array
        (
            [col1] => val1
            [col2] => val2
            [col3] => val3
            [score] => 1
        )
 
    [1] => Array
        (
            [col1] => val4
            [col2] => val5
            [col3] => val6
            [score] => 0.9
        )
 
)
Now you will have a basic Solr library installed.  There are more advanced things you can do with the library such as pagination, facetting and date ranges, which I shall cover in a future post.
 */
?>