<?php


/*

DON'T EDIT THIS FILE!

This file was automatically generated by the Lime parser generator.
The real source code you should be looking at is in one or more
grammar files in the Lime format.

THE ONLY REASON TO LOOK AT THIS FILE is to see where in the grammar
file that your error happened, because there are enough comments to
help you debug your grammar.

If you ignore this warning, you're shooting yourself in the brain,
not the foot.

*/

class spectql extends lime_parser {
var $qi = 0;
var $i = array (
  0 => 
  array (
    'flow' => 's 1',
    'expression' => 's 5',
    'resource' => 's 16',
    'resourceid' => 's 17',
    'name' => 's 83',
    'stmt' => 's 95',
    '\'start\'' => 'a \'start\'',
  ),
  1 => 
  array (
    '\':\'' => 's 2',
    '#' => 'r 0',
  ),
  2 => 
  array (
    'format' => 's 3',
    'name' => 's 4',
  ),
  3 => 
  array (
    '#' => 'r 1',
  ),
  4 => 
  array (
    '#' => 'r 49',
  ),
  5 => 
  array (
    '\'.\'' => 's 6',
    '\':\'' => 'r 2',
    '#' => 'r 2',
  ),
  6 => 
  array (
    'LIMIT' => 's 7',
  ),
  7 => 
  array (
    '\'(\'' => 's 8',
  ),
  8 => 
  array (
    'num' => 's 9',
    '\')\'' => 'r 14',
    '\'.\'' => 'r 14',
    '\',\'' => 'r 14',
  ),
  9 => 
  array (
    '\',\'' => 's 10',
    '\')\'' => 's 15',
    '\'.\'' => 's 13',
  ),
  10 => 
  array (
    'num' => 's 11',
    '\')\'' => 'r 14',
    '\'.\'' => 'r 14',
  ),
  11 => 
  array (
    '\')\'' => 's 12',
    '\'.\'' => 's 13',
  ),
  12 => 
  array (
    '\':\'' => 'r 3',
    '#' => 'r 3',
  ),
  13 => 
  array (
    'num' => 's 14',
    '\'.\'' => 'r 14',
    '\')\'' => 'r 14',
    '\',\'' => 'r 14',
    '\'?\'' => 'r 14',
    '\'/\'' => 'r 14',
    '\'{\'' => 'r 14',
    '\':\'' => 'r 14',
    '\'+\'' => 'r 14',
    '\'-\'' => 'r 14',
    '\'}\'' => 'r 14',
    '\'&\'' => 'r 14',
    '\'|\'' => 'r 14',
    '#' => 'r 14',
  ),
  14 => 
  array (
    '\'.\'' => 'r 13',
    '\')\'' => 'r 13',
    '\',\'' => 'r 13',
    '\'?\'' => 'r 13',
    '\'/\'' => 'r 13',
    '\'{\'' => 'r 13',
    '\':\'' => 'r 13',
    '\'+\'' => 'r 13',
    '\'-\'' => 'r 13',
    '\'}\'' => 'r 13',
    '\'&\'' => 'r 13',
    '\'|\'' => 'r 13',
    '#' => 'r 13',
  ),
  15 => 
  array (
    '\':\'' => 'r 4',
    '#' => 'r 4',
  ),
  16 => 
  array (
    '\'.\'' => 'r 5',
    '\':\'' => 'r 5',
    '#' => 'r 5',
  ),
  17 => 
  array (
    '\'{\'' => 's 18',
    '\'?\'' => 's 93',
    '\'/\'' => 's 87',
    '\'.\'' => 'r 8',
    '\':\'' => 'r 8',
    '#' => 'r 8',
  ),
  18 => 
  array (
    'selector' => 's 19',
    'num' => 's 60',
    'selectargument' => 's 92',
    'argument' => 's 62',
    'name' => 's 66',
    'link' => 's 79',
    '\'*\'' => 's 80',
    'function' => 's 81',
    '\'.\'' => 'r 14',
    '\'+\'' => 'r 14',
    '\'-\'' => 'r 14',
    '\',\'' => 'r 14',
    '\'}\'' => 'r 14',
  ),
  19 => 
  array (
    '\'}\'' => 's 20',
    '\',\'' => 's 59',
  ),
  20 => 
  array (
    '\'?\'' => 's 21',
    '\'.\'' => 'r 6',
    '\':\'' => 'r 6',
    '#' => 'r 6',
  ),
  21 => 
  array (
    'filterlist' => 's 22',
    'filter' => 's 24',
    '\'(\'' => 's 28',
    'name' => 's 31',
  ),
  22 => 
  array (
    '\'&\'' => 's 23',
    '\'|\'' => 's 26',
    '\'.\'' => 'r 7',
    '\':\'' => 'r 7',
    '#' => 'r 7',
  ),
  23 => 
  array (
    'filter' => 's 24',
    'filterlist' => 's 25',
    '\'(\'' => 's 28',
    'name' => 's 31',
  ),
  24 => 
  array (
    '\'&\'' => 'r 15',
    '\'|\'' => 'r 15',
    '\'.\'' => 'r 15',
    '\':\'' => 'r 15',
    '\')\'' => 'r 15',
    '#' => 'r 15',
  ),
  25 => 
  array (
    '\'&\'' => 'r 16',
    '\'|\'' => 's 26',
    '\'.\'' => 'r 16',
    '\':\'' => 'r 16',
    '\')\'' => 'r 16',
    '#' => 'r 16',
  ),
  26 => 
  array (
    'filter' => 's 24',
    'filterlist' => 's 27',
    '\'(\'' => 's 28',
    'name' => 's 31',
  ),
  27 => 
  array (
    '\'&\'' => 'r 17',
    '\'|\'' => 'r 17',
    '\'.\'' => 'r 17',
    '\':\'' => 'r 17',
    '\')\'' => 'r 17',
    '#' => 'r 17',
  ),
  28 => 
  array (
    'filter' => 's 24',
    'filterlist' => 's 29',
    '\'(\'' => 's 28',
    'name' => 's 31',
  ),
  29 => 
  array (
    '\'&\'' => 's 23',
    '\'|\'' => 's 26',
    '\')\'' => 's 30',
  ),
  30 => 
  array (
    '\'|\'' => 'r 18',
    '\'&\'' => 'r 18',
    '\'.\'' => 'r 18',
    '\':\'' => 'r 18',
    '\')\'' => 'r 18',
    '#' => 'r 18',
  ),
  31 => 
  array (
    '\'>\'' => 's 32',
    'EQ' => 's 35',
    '\'<\'' => 's 38',
    '\'~\'' => 's 41',
    'NE' => 's 43',
    'GE' => 's 46',
    'LE' => 's 49',
    '\'(\'' => 's 52',
  ),
  32 => 
  array (
    'num' => 's 33',
    'string' => 's 34',
    '\'.\'' => 'r 14',
    '\'&\'' => 'r 14',
    '\'|\'' => 'r 14',
    '\':\'' => 'r 14',
    '\')\'' => 'r 14',
    '#' => 'r 14',
  ),
  33 => 
  array (
    '\'.\'' => 'r 22',
    '\'&\'' => 'r 22',
    '\'|\'' => 'r 22',
    '\':\'' => 'r 22',
    '\')\'' => 'r 22',
    '#' => 'r 22',
  ),
  34 => 
  array (
    '\'&\'' => 'r 19',
    '\'|\'' => 'r 19',
    '\'.\'' => 'r 19',
    '\':\'' => 'r 19',
    '\')\'' => 'r 19',
    '#' => 'r 19',
  ),
  35 => 
  array (
    'num' => 's 36',
    'string' => 's 37',
    '\'.\'' => 'r 14',
    '\'&\'' => 'r 14',
    '\'|\'' => 'r 14',
    '\':\'' => 'r 14',
    '\')\'' => 'r 14',
    '#' => 'r 14',
  ),
  36 => 
  array (
    '\'.\'' => 'r 23',
    '\'&\'' => 'r 23',
    '\'|\'' => 'r 23',
    '\':\'' => 'r 23',
    '\')\'' => 'r 23',
    '#' => 'r 23',
  ),
  37 => 
  array (
    '\'&\'' => 'r 20',
    '\'|\'' => 'r 20',
    '\'.\'' => 'r 20',
    '\':\'' => 'r 20',
    '\')\'' => 'r 20',
    '#' => 'r 20',
  ),
  38 => 
  array (
    'num' => 's 39',
    'string' => 's 40',
    '\'.\'' => 'r 14',
    '\'&\'' => 'r 14',
    '\'|\'' => 'r 14',
    '\':\'' => 'r 14',
    '\')\'' => 'r 14',
    '#' => 'r 14',
  ),
  39 => 
  array (
    '\'.\'' => 'r 24',
    '\'&\'' => 'r 24',
    '\'|\'' => 'r 24',
    '\':\'' => 'r 24',
    '\')\'' => 'r 24',
    '#' => 'r 24',
  ),
  40 => 
  array (
    '\'&\'' => 'r 21',
    '\'|\'' => 'r 21',
    '\'.\'' => 'r 21',
    '\':\'' => 'r 21',
    '\')\'' => 'r 21',
    '#' => 'r 21',
  ),
  41 => 
  array (
    'string' => 's 42',
  ),
  42 => 
  array (
    '\'&\'' => 'r 25',
    '\'|\'' => 'r 25',
    '\'.\'' => 'r 25',
    '\':\'' => 'r 25',
    '\')\'' => 'r 25',
    '#' => 'r 25',
  ),
  43 => 
  array (
    'num' => 's 44',
    'string' => 's 45',
    '\'.\'' => 'r 14',
    '\'&\'' => 'r 14',
    '\'|\'' => 'r 14',
    '\':\'' => 'r 14',
    '\')\'' => 'r 14',
    '#' => 'r 14',
  ),
  44 => 
  array (
    '\'.\'' => 's 13',
    '\'&\'' => 'r 27',
    '\'|\'' => 'r 27',
    '\':\'' => 'r 27',
    '\')\'' => 'r 27',
    '#' => 'r 27',
  ),
  45 => 
  array (
    '\'&\'' => 'r 26',
    '\'|\'' => 'r 26',
    '\'.\'' => 'r 26',
    '\':\'' => 'r 26',
    '\')\'' => 'r 26',
    '#' => 'r 26',
  ),
  46 => 
  array (
    'num' => 's 47',
    'string' => 's 48',
    '\'.\'' => 'r 14',
    '\'&\'' => 'r 14',
    '\'|\'' => 'r 14',
    '\':\'' => 'r 14',
    '\')\'' => 'r 14',
    '#' => 'r 14',
  ),
  47 => 
  array (
    '\'.\'' => 's 13',
    '\'&\'' => 'r 29',
    '\'|\'' => 'r 29',
    '\':\'' => 'r 29',
    '\')\'' => 'r 29',
    '#' => 'r 29',
  ),
  48 => 
  array (
    '\'&\'' => 'r 28',
    '\'|\'' => 'r 28',
    '\'.\'' => 'r 28',
    '\':\'' => 'r 28',
    '\')\'' => 'r 28',
    '#' => 'r 28',
  ),
  49 => 
  array (
    'num' => 's 50',
    'string' => 's 51',
    '\'.\'' => 'r 14',
    '\'&\'' => 'r 14',
    '\'|\'' => 'r 14',
    '\':\'' => 'r 14',
    '\')\'' => 'r 14',
    '#' => 'r 14',
  ),
  50 => 
  array (
    '\'.\'' => 's 13',
    '\'&\'' => 'r 31',
    '\'|\'' => 'r 31',
    '\':\'' => 'r 31',
    '\')\'' => 'r 31',
    '#' => 'r 31',
  ),
  51 => 
  array (
    '\'&\'' => 'r 30',
    '\'|\'' => 'r 30',
    '\'.\'' => 'r 30',
    '\':\'' => 'r 30',
    '\')\'' => 'r 30',
    '#' => 'r 30',
  ),
  52 => 
  array (
    'num' => 's 53',
    '\',\'' => 'r 14',
    '\'.\'' => 'r 14',
  ),
  53 => 
  array (
    '\'.\'' => 's 13',
    '\',\'' => 's 54',
  ),
  54 => 
  array (
    'num' => 's 55',
    '\',\'' => 'r 14',
    '\'.\'' => 'r 14',
  ),
  55 => 
  array (
    '\'.\'' => 's 13',
    '\',\'' => 's 56',
  ),
  56 => 
  array (
    'num' => 's 57',
    '\')\'' => 'r 14',
    '\'.\'' => 'r 14',
  ),
  57 => 
  array (
    '\'.\'' => 's 13',
    '\')\'' => 's 58',
  ),
  58 => 
  array (
    '\'&\'' => 'r 32',
    '\'|\'' => 'r 32',
    '\'.\'' => 'r 32',
    '\':\'' => 'r 32',
    '\')\'' => 'r 32',
    '#' => 'r 32',
  ),
  59 => 
  array (
    'num' => 's 60',
    'selectargument' => 's 61',
    'argument' => 's 62',
    'name' => 's 66',
    'link' => 's 79',
    '\'*\'' => 's 80',
    'function' => 's 81',
    '\'.\'' => 'r 14',
    '\'+\'' => 'r 14',
    '\'-\'' => 'r 14',
    '\'}\'' => 'r 14',
    '\',\'' => 'r 14',
  ),
  60 => 
  array (
    '\'.\'' => 's 13',
    '\'+\'' => 'r 47',
    '\'-\'' => 'r 47',
    '\',\'' => 'r 47',
    '\'}\'' => 'r 47',
    '\')\'' => 'r 47',
  ),
  61 => 
  array (
    '\'}\'' => 'r 34',
    '\',\'' => 'r 34',
  ),
  62 => 
  array (
    'order' => 's 63',
    '\'+\'' => 's 64',
    '\'-\'' => 's 65',
    '\',\'' => 'r 35',
    '\'}\'' => 'r 35',
  ),
  63 => 
  array (
    '\',\'' => 'r 36',
    '\'}\'' => 'r 36',
  ),
  64 => 
  array (
    '\',\'' => 'r 41',
    '\'}\'' => 'r 41',
  ),
  65 => 
  array (
    '\',\'' => 'r 42',
    '\'}\'' => 'r 42',
  ),
  66 => 
  array (
    'ALIAS' => 's 67',
    '\'(\'' => 's 71',
    'LN' => 's 82',
    '\'+\'' => 'r 43',
    '\'-\'' => 'r 43',
    '\',\'' => 'r 43',
    '\'}\'' => 'r 43',
  ),
  67 => 
  array (
    'num' => 's 60',
    'argument' => 's 68',
    'name' => 's 70',
    'link' => 's 79',
    '\'*\'' => 's 80',
    'function' => 's 81',
    '\'.\'' => 'r 14',
    '\'+\'' => 'r 14',
    '\'-\'' => 'r 14',
    '\',\'' => 'r 14',
    '\'}\'' => 'r 14',
  ),
  68 => 
  array (
    'order' => 's 69',
    '\'+\'' => 's 64',
    '\'-\'' => 's 65',
    '\',\'' => 'r 37',
    '\'}\'' => 'r 37',
  ),
  69 => 
  array (
    '\',\'' => 'r 38',
    '\'}\'' => 'r 38',
  ),
  70 => 
  array (
    '\'(\'' => 's 71',
    'LN' => 's 82',
    '\'+\'' => 'r 43',
    '\'-\'' => 'r 43',
    '\',\'' => 'r 43',
    '\'}\'' => 'r 43',
    '\')\'' => 'r 43',
  ),
  71 => 
  array (
    'num' => 's 60',
    'name' => 's 70',
    'argument' => 's 72',
    'link' => 's 79',
    '\'*\'' => 's 80',
    'function' => 's 81',
    '\'.\'' => 'r 14',
    '\',\'' => 'r 14',
    '\')\'' => 'r 14',
  ),
  72 => 
  array (
    '\')\'' => 's 73',
    '\',\'' => 's 74',
  ),
  73 => 
  array (
    '\'+\'' => 'r 39',
    '\'-\'' => 'r 39',
    '\',\'' => 'r 39',
    '\'}\'' => 'r 39',
    '\')\'' => 'r 39',
  ),
  74 => 
  array (
    'num' => 's 60',
    'name' => 's 70',
    'argument' => 's 75',
    'link' => 's 79',
    '\'*\'' => 's 80',
    'function' => 's 81',
    '\'.\'' => 'r 14',
    '\',\'' => 'r 14',
  ),
  75 => 
  array (
    '\',\'' => 's 76',
  ),
  76 => 
  array (
    'num' => 's 60',
    'name' => 's 70',
    'argument' => 's 77',
    'link' => 's 79',
    '\'*\'' => 's 80',
    'function' => 's 81',
    '\'.\'' => 'r 14',
    '\')\'' => 'r 14',
  ),
  77 => 
  array (
    '\')\'' => 's 78',
  ),
  78 => 
  array (
    '\'+\'' => 'r 40',
    '\'-\'' => 'r 40',
    '\',\'' => 'r 40',
    '\'}\'' => 'r 40',
    '\')\'' => 'r 40',
  ),
  79 => 
  array (
    '\'+\'' => 'r 44',
    '\'-\'' => 'r 44',
    '\',\'' => 'r 44',
    '\'}\'' => 'r 44',
    '\')\'' => 'r 44',
  ),
  80 => 
  array (
    '\'+\'' => 'r 45',
    '\'-\'' => 'r 45',
    '\',\'' => 'r 45',
    '\'}\'' => 'r 45',
    '\')\'' => 'r 45',
  ),
  81 => 
  array (
    '\'+\'' => 'r 46',
    '\'-\'' => 'r 46',
    '\',\'' => 'r 46',
    '\'}\'' => 'r 46',
    '\')\'' => 'r 46',
  ),
  82 => 
  array (
    'name' => 's 83',
    'resourceid' => 's 86',
  ),
  83 => 
  array (
    '\'/\'' => 's 84',
  ),
  84 => 
  array (
    'name' => 's 85',
  ),
  85 => 
  array (
    '\'/\'' => 'r 10',
    '\'?\'' => 'r 10',
    '\'{\'' => 'r 10',
    '\'.\'' => 'r 10',
    '\':\'' => 'r 10',
    '#' => 'r 10',
  ),
  86 => 
  array (
    '\'/\'' => 's 87',
    '\'.\'' => 's 90',
  ),
  87 => 
  array (
    'name' => 's 88',
    'num' => 's 89',
    '\'.\'' => 'r 14',
    '\'?\'' => 'r 14',
    '\'/\'' => 'r 14',
    '\'{\'' => 'r 14',
    '\':\'' => 'r 14',
    '#' => 'r 14',
  ),
  88 => 
  array (
    '\'/\'' => 'r 11',
    '\'?\'' => 'r 11',
    '\'{\'' => 'r 11',
    '\'.\'' => 'r 11',
    '\':\'' => 'r 11',
    '#' => 'r 11',
  ),
  89 => 
  array (
    '\'.\'' => 'r 12',
    '\'?\'' => 'r 12',
    '\'/\'' => 'r 12',
    '\'{\'' => 'r 12',
    '\':\'' => 'r 12',
    '#' => 'r 12',
  ),
  90 => 
  array (
    'name' => 's 91',
  ),
  91 => 
  array (
    '\'+\'' => 'r 48',
    '\'-\'' => 'r 48',
    '\',\'' => 'r 48',
    '\'}\'' => 'r 48',
    '\')\'' => 'r 48',
  ),
  92 => 
  array (
    '\',\'' => 'r 33',
    '\'}\'' => 'r 33',
  ),
  93 => 
  array (
    'filterlist' => 's 94',
    'filter' => 's 24',
    '\'(\'' => 's 28',
    'name' => 's 31',
  ),
  94 => 
  array (
    '\'&\'' => 's 23',
    '\'|\'' => 's 26',
    '\'.\'' => 'r 9',
    '\':\'' => 'r 9',
    '#' => 'r 9',
  ),
  95 => 
  array (
    '#' => 'r 57',
  ),
);
function reduce_0_stmt_1($tokens, &$result) {
#
# (0) stmt :=  flow
#
$result = reset($tokens);

}

function reduce_1_stmt_2($tokens, &$result) {
#
# (1) stmt :=  flow  ':'  format
#
$result = reset($tokens);

}

function reduce_2_flow_1($tokens, &$result) {
#
# (2) flow :=  expression
#
$result = reset($tokens);

}

function reduce_3_flow_2($tokens, &$result) {
#
# (3) flow :=  expression  '.'  LIMIT  '('  num  ','  num  ')'
#
$result = reset($tokens);
$e =& $tokens[0];
$offset =& $tokens[4];
$limit =& $tokens[6];
 

    $limit = new LimitFilter($e,$offset,$limit);
    $result = $limit;
 
}

function reduce_4_flow_3($tokens, &$result) {
#
# (4) flow :=  expression  '.'  LIMIT  '('  num  ')'
#
$result = reset($tokens);
$e =& $tokens[0];
$limit =& $tokens[4];

    $limit = new LimitFilter($e,0,$limit);
    $result = $limit;

}

function reduce_5_expression_1($tokens, &$result) {
#
# (5) expression :=  resource
#
$result = reset($tokens);
$result = $tokens[0];
}

function reduce_6_resource_1($tokens, &$result) {
#
# (6) resource :=  resourceid  '{'  selector  '}'
#
$result = reset($tokens);
$sel =& $tokens[2];
 


// from
$totalfilter = new Identifier($tokens[0]);
	
// group by
$groupby = array();
$aggregate = false; 
foreach($sel["identifiers"] as $identifier){
    if(get_class($identifier) == "AggregatorFunction"){
        $aggregate = true;
    }else{
			// identifier could be a unairy or tertiary function like substring or ucase
			if(get_class($identifier) != "Identifier"){
				array_push($groupby,$identifier->getSource());				
			}else{
			   array_push($groupby,$identifier);
			}	
      
    }					
}
							
if($aggregate){
    $datagrouper = new DataGrouper($groupby);
    $totalfilter = putFilterAfterIfExists($totalfilter,$datagrouper);
}
	
// select

$selecttables = new ColumnSelectionFilter($sel["filters"]);
$totalfilter = putFilterAfterIfExists($totalfilter,$selecttables);


// order by
$orderby = new SortFieldsFilter($sel["sorts"]);
if(!empty($sel["sorts"])){
    $totalfilter = putFilterAfterIfExists($totalfilter,$orderby);
}

$result = $totalfilter;
						
}

function reduce_7_resource_2($tokens, &$result) {
#
# (7) resource :=  resourceid  '{'  selector  '}'  '?'  filterlist
#
$result = reset($tokens);
$sel =& $tokens[2];
$fl =& $tokens[5];
 


$totalfilter = new Identifier($tokens[0]);


// where

$expressionFilter = new FilterByExpressionFilter($fl); 
$totalfilter = putFilterAfterIfExists($totalfilter,$expressionFilter);

$groupby = array();
$aggregate = false; 
foreach($sel["identifiers"] as $identifier){
    if(get_class($identifier) == "AggregatorFunction"){
        $aggregate = true;
    }else{
        array_push($groupby,$identifier);
    }					
}

// group by				
			
if($aggregate){
    $datagrouper = new DataGrouper($groupby);
    $totalfilter = putFilterAfterIfExists($totalfilter,$datagrouper);
}
	
// functions (binary, unary or tertiary)

$selecttables = new ColumnSelectionFilter($sel["filters"]);
$totalfilter = putFilterAfterIfExists($totalfilter,$selecttables);

// order by
$orderby = new SortFieldsFilter($sel["sorts"]);
if(!empty($sel["sorts"])){
    $totalfilter = putFilterAfterIfExists($totalfilter,$orderby);
}

$result = $totalfilter;				

}

function reduce_8_resource_3($tokens, &$result) {
#
# (8) resource :=  resourceid
#
$result = reset($tokens);
 $result = new Identifier($tokens[0]); 
}

function reduce_9_resource_4($tokens, &$result) {
#
# (9) resource :=  resourceid  '?'  filterlist
#
$result = reset($tokens);
 $result = new FilterByExpressionFilter($tokens[2]); $result->setSource(new Identifier($tokens[0])); 
}

function reduce_10_resourceid_1($tokens, &$result) {
#
# (10) resourceid :=  name  '/'  name
#
$result = reset($tokens);
 $result = $tokens[0] . "." . $tokens[2]; 
}

function reduce_11_resourceid_2($tokens, &$result) {
#
# (11) resourceid :=  resourceid  '/'  name
#
$result = reset($tokens);
 $result = $tokens[0] . "." . $tokens[2]; 
}

function reduce_12_resourceid_3($tokens, &$result) {
#
# (12) resourceid :=  resourceid  '/'  num
#
$result = reset($tokens);
 $result = $tokens[0] . "." . $tokens[2]; 
}

function reduce_13_num_1($tokens, &$result) {
#
# (13) num :=  num  '.'  num
#
$result = reset($tokens);
 $result = new Constant((double) ($tokens[0] . "." . $tokens[2]));  
}

function reduce_14_num_2($tokens, &$result) {
#
# (14) num :=
#
$result = reset($tokens);

}

function reduce_15_filterlist_1($tokens, &$result) {
#
# (15) filterlist :=  filter
#
$result = reset($tokens);
 $result = $tokens[0]; 
}

function reduce_16_filterlist_2($tokens, &$result) {
#
# (16) filterlist :=  filterlist  '&'  filterlist
#
$result = reset($tokens);
 $result = new BinaryFunction(BinaryFunction::$FUNCTION_BINARY_AND,$tokens[0],$tokens[2]); 
}

function reduce_17_filterlist_3($tokens, &$result) {
#
# (17) filterlist :=  filterlist  '|'  filterlist
#
$result = reset($tokens);
 $result = new BinaryFunction(BinaryFunction::$FUNCTION_BINARY_OR,$tokens[0],$tokens[2]); 
}

function reduce_18_filterlist_4($tokens, &$result) {
#
# (18) filterlist :=  '('  filterlist  ')'
#
$result = reset($tokens);
$list =& $tokens[1];
 $result = $list;
}

function reduce_19_filter_1($tokens, &$result) {
#
# (19) filter :=  name  '>'  string
#
$result = reset($tokens);
$a =& $tokens[0];
$b =& $tokens[2];
 $result =  new BinaryFunction(BinaryFunction::$FUNCTION_BINARY_COMPARE_SMALLER_THAN, new Identifier($a),new Constant($b)); 
}

function reduce_20_filter_2($tokens, &$result) {
#
# (20) filter :=  name  EQ  string
#
$result = reset($tokens);
$a =& $tokens[0];
$b =& $tokens[2];
 $result = new BinaryFunction(BinaryFunction::$FUNCTION_BINARY_COMPARE_EQUAL,new Identifier($a),new Constant($b)); 
}

function reduce_21_filter_3($tokens, &$result) {
#
# (21) filter :=  name  '<'  string
#
$result = reset($tokens);
$a =& $tokens[0];
$b =& $tokens[2];
 $result = new BinaryFunction(BinaryFunction::$FUNCTION_BINARY_COMPARE_SMALLER_THAN, new Identifier($a),new Constant($b)); 
}

function reduce_22_filter_4($tokens, &$result) {
#
# (22) filter :=  name  '>'  num
#
$result = reset($tokens);
$a =& $tokens[0];
$b =& $tokens[2];
 $result = new BinaryFunction(BinaryFunction::$FUNCTION_BINARY_COMPARE_LARGER_THAN, new Identifier($a),new Constant($b)); 
}

function reduce_23_filter_5($tokens, &$result) {
#
# (23) filter :=  name  EQ  num
#
$result = reset($tokens);
$a =& $tokens[0];
$b =& $tokens[2];
 $result = new BinaryFunction(BinaryFunction::$FUNCTION_BINARY_COMPARE_EQUAL,new Identifier($a),new Constant($b)); 
}

function reduce_24_filter_6($tokens, &$result) {
#
# (24) filter :=  name  '<'  num
#
$result = reset($tokens);
$a =& $tokens[0];
$b =& $tokens[2];
 $result = new BinaryFunction(BinaryFunction::$FUNCTION_BINARY_COMPARE_SMALLER_THAN, new Identifier($a),new Constant($b)); 
}

function reduce_25_filter_7($tokens, &$result) {
#
# (25) filter :=  name  '~'  string
#
$result = reset($tokens);
$a =& $tokens[0];
$b =& $tokens[2];
 $result = new BinaryFunction(BinaryFunction::$FUNCTION_BINARY_MATCH_REGEX, new Identifier($a),new Constant("/.*".preg_quote($b).".*/")); 
}

function reduce_26_filter_8($tokens, &$result) {
#
# (26) filter :=  name  NE  string
#
$result = reset($tokens);
$a =& $tokens[0];
$b =& $tokens[2];
 $result = new BinaryFunction(BinaryFunction::$FUNCTION_BINARY_COMPARE_NOTEQUAL,new Identifier($a),new Constant($b)); 
}

function reduce_27_filter_9($tokens, &$result) {
#
# (27) filter :=  name  NE  num
#
$result = reset($tokens);
$a =& $tokens[0];
$b =& $tokens[2];
 $result = new BinaryFunction(BinaryFunction::$FUNCTION_BINARY_COMPARE_NOTEQUAL,new Identifier($a),new Constant($b)); 
}

function reduce_28_filter_10($tokens, &$result) {
#
# (28) filter :=  name  GE  string
#
$result = reset($tokens);
$a =& $tokens[0];
$b =& $tokens[2];
 $result = new BinaryFunction(BinaryFunction::$FUNCTION_BINARY_COMPARE_LARGER_OR_EQUAL_THAN,new Identifier($a),new Constant($b)); 
}

function reduce_29_filter_11($tokens, &$result) {
#
# (29) filter :=  name  GE  num
#
$result = reset($tokens);
$a =& $tokens[0];
$b =& $tokens[2];
 $result = new BinaryFunction(BinaryFunction::$FUNCTION_BINARY_COMPARE_LARGER_OR_EQUAL_THAN,new Identifier($a),new Constant($b)); 
}

function reduce_30_filter_12($tokens, &$result) {
#
# (30) filter :=  name  LE  string
#
$result = reset($tokens);
$a =& $tokens[0];
$b =& $tokens[2];
 $result = new BinaryFunction(BinaryFunction::$FUNCTION_BINARY_COMPARE_SMALLER_OR_EQUAL_THAN,new Identifier($a),new Constant($b)); 
}

function reduce_31_filter_13($tokens, &$result) {
#
# (31) filter :=  name  LE  num
#
$result = reset($tokens);
$a =& $tokens[0];
$b =& $tokens[2];
 $result = new BinaryFunction(BinaryFunction::$FUNCTION_BINARY_COMPARE_SMALLER_OR_EQUAL_THAN,new Identifier($a),new Constant($b)); 
}

function reduce_32_filter_14($tokens, &$result) {
#
# (32) filter :=  name  '('  num  ','  num  ','  num  ')'
#
$result = reset($tokens);
$function =& $tokens[0];
$lat =& $tokens[2];
$long =& $tokens[4];
$radius =& $tokens[6];
 /* TODO in radius function (ternary function)*/ 
}

function reduce_33_selector_1($tokens, &$result) {
#
# (33) selector :=  selectargument
#
$result = reset($tokens);
$arg =& $tokens[0];
 $result = $tokens[0]; 
}

function reduce_34_selector_2($tokens, &$result) {
#
# (34) selector :=  selector  ','  selectargument
#
$result = reset($tokens);
$arg =& $tokens[2];
 $filters = array_merge($tokens[0]["filters"],$tokens[2]["filters"]); 
                                    $sorts   = array_merge($tokens[0]["sorts"],$tokens[2]["sorts"]);
				    $identifiers = array_merge($tokens[0]["identifiers"],$tokens[2]["identifiers"]);
                                    $result = array("filters"=>$filters,"sorts"=>$sorts, "identifiers" => $identifiers); 
}

function reduce_35_selectargument_1($tokens, &$result) {
#
# (35) selectargument :=  argument
#
$result = reset($tokens);
$arg =& $tokens[0];
 $result = array("filters" => array(new ColumnSelectionFilterColumn($arg,null)), "sorts" => array(), "identifiers" => array($arg)); 
}

function reduce_36_selectargument_2($tokens, &$result) {
#
# (36) selectargument :=  argument  order
#
$result = reset($tokens);
$arg =& $tokens[0];
$order =& $tokens[1];
 

$result =  array( "filters" => array(new ColumnSelectionFilterColumn($arg,null)), 
             "sorts" => array(new SortFieldsFilterColumn($arg, $order)), 
             "identifiers" => array($arg));
}

function reduce_37_selectargument_3($tokens, &$result) {
#
# (37) selectargument :=  name  ALIAS  argument
#
$result = reset($tokens);
$arg =& $tokens[2];
$result = array("filters" => array(new ColumnSelectionFilterColumn($arg,$tokens[0])), "sorts" => array(), "identifiers" => array($arg));
}

function reduce_38_selectargument_4($tokens, &$result) {
#
# (38) selectargument :=  name  ALIAS  argument  order
#
$result = reset($tokens);
$arg =& $tokens[2];
$result = array("filters" => array(new ColumnSelectionFilterColumn($arg,$tokens[0])), "sorts" => array(new SortFieldsFilterColumn($arg, $order)), "identifiers" => array($arg));
}

function reduce_39_function_1($tokens, &$result) {
#
# (39) function :=  name  '('  argument  ')'
#
$result = reset($tokens);
 $result = getUnaryFilterForSQLFunction($tokens[0],$tokens[2]); 
}

function reduce_40_function_2($tokens, &$result) {
#
# (40) function :=  name  '('  argument  ','  argument  ','  argument  ')'
#
$result = reset($tokens);
$arg1 =& $tokens[2];
$arg2 =& $tokens[4];
$arg3 =& $tokens[6];
$result = getTertairyFunctionForSQLFunction($tokens[0],$arg1,$arg2,$arg3);
}

function reduce_41_order_1($tokens, &$result) {
#
# (41) order :=  '+'
#
$result = reset($tokens);
 /* SORT BY ascending */ $result = SortFieldsFilterColumn::$SORTORDER_ASCENDING; 
}

function reduce_42_order_2($tokens, &$result) {
#
# (42) order :=  '-'
#
$result = reset($tokens);
 /* SORT BY descending  */ $result = SortFieldsFilterColumn::$SORTORDER_DESCENDING; 
}

function reduce_43_argument_1($tokens, &$result) {
#
# (43) argument :=  name
#
$result = reset($tokens);
$name =& $tokens[0];
 $result = new Identifier($name); 
}

function reduce_44_argument_2($tokens, &$result) {
#
# (44) argument :=  link
#
$result = reset($tokens);

}

function reduce_45_argument_3($tokens, &$result) {
#
# (45) argument :=  '*'
#
$result = reset($tokens);
 $result = new Identifier('*'); 
}

function reduce_46_argument_4($tokens, &$result) {
#
# (46) argument :=  function
#
$result = reset($tokens);
 $result = $tokens[0];
}

function reduce_47_argument_5($tokens, &$result) {
#
# (47) argument :=  num
#
$result = reset($tokens);
$result = new Constant($tokens[0]);
}

function reduce_48_link_1($tokens, &$result) {
#
# (48) link :=  name  LN  resourceid  '.'  name
#
$result = reset($tokens);
 /* joined resource */ 
}

function reduce_49_format_1($tokens, &$result) {
#
# (49) format :=  name
#
$result = reset($tokens);
 /* do nothing, format isnt used in the AST for it is an abstract filter tree , format is not a filter.*/ 
}

function reduce_50_calc_1($tokens, &$result) {
#
# (50) calc :=  num
#
$result = reset($tokens);
 $result = new Constant($tokens[0]); 
}

function reduce_51_calc_2($tokens, &$result) {
#
# (51) calc :=  calc  '+'  calc
#
$result = reset($tokens);
 $result = new BinaryFunction(BinaryFunction::$FUNCTION_BINARY_PLUS, $tokens[0], $tokens[2]);  
}

function reduce_52_calc_3($tokens, &$result) {
#
# (52) calc :=  calc  '-'  calc
#
$result = reset($tokens);
 $result = new BinaryFunction(BinaryFunction::$FUNCTION_BINARY_MINUS, $tokens[0], $tokens[2]); 
}

function reduce_53_calc_4($tokens, &$result) {
#
# (53) calc :=  calc  '*'  calc
#
$result = reset($tokens);
 $result = new BinaryFunction(BinaryFunction::$FUNCTION_BINARY_MULTIPLY, $tokens[0], $tokens[2]); 
}

function reduce_54_calc_5($tokens, &$result) {
#
# (54) calc :=  calc  '/'  calc
#
$result = reset($tokens);
 $result = new BinaryFunction(BinaryFunction::$FUNCTION_BINARY_DIVIDE, $tokens[0], $tokens[2]); 
}

function reduce_55_calc_6($tokens, &$result) {
#
# (55) calc :=  num  '.'  num
#
$result = reset($tokens);
 $result = new Constant((double)($tokens[0] . "." . $tokens[2])); 
}

function reduce_56_calc_7($tokens, &$result) {
#
# (56) calc :=  '('  calc  ')'
#
$result = reset($tokens);
$result = $tokens[0];
}

function reduce_57_start_1($tokens, &$result) {
#
# (57) 'start' :=  stmt
#
$result = reset($tokens);

}

var $method = array (
  0 => 'reduce_0_stmt_1',
  1 => 'reduce_1_stmt_2',
  2 => 'reduce_2_flow_1',
  3 => 'reduce_3_flow_2',
  4 => 'reduce_4_flow_3',
  5 => 'reduce_5_expression_1',
  6 => 'reduce_6_resource_1',
  7 => 'reduce_7_resource_2',
  8 => 'reduce_8_resource_3',
  9 => 'reduce_9_resource_4',
  10 => 'reduce_10_resourceid_1',
  11 => 'reduce_11_resourceid_2',
  12 => 'reduce_12_resourceid_3',
  13 => 'reduce_13_num_1',
  14 => 'reduce_14_num_2',
  15 => 'reduce_15_filterlist_1',
  16 => 'reduce_16_filterlist_2',
  17 => 'reduce_17_filterlist_3',
  18 => 'reduce_18_filterlist_4',
  19 => 'reduce_19_filter_1',
  20 => 'reduce_20_filter_2',
  21 => 'reduce_21_filter_3',
  22 => 'reduce_22_filter_4',
  23 => 'reduce_23_filter_5',
  24 => 'reduce_24_filter_6',
  25 => 'reduce_25_filter_7',
  26 => 'reduce_26_filter_8',
  27 => 'reduce_27_filter_9',
  28 => 'reduce_28_filter_10',
  29 => 'reduce_29_filter_11',
  30 => 'reduce_30_filter_12',
  31 => 'reduce_31_filter_13',
  32 => 'reduce_32_filter_14',
  33 => 'reduce_33_selector_1',
  34 => 'reduce_34_selector_2',
  35 => 'reduce_35_selectargument_1',
  36 => 'reduce_36_selectargument_2',
  37 => 'reduce_37_selectargument_3',
  38 => 'reduce_38_selectargument_4',
  39 => 'reduce_39_function_1',
  40 => 'reduce_40_function_2',
  41 => 'reduce_41_order_1',
  42 => 'reduce_42_order_2',
  43 => 'reduce_43_argument_1',
  44 => 'reduce_44_argument_2',
  45 => 'reduce_45_argument_3',
  46 => 'reduce_46_argument_4',
  47 => 'reduce_47_argument_5',
  48 => 'reduce_48_link_1',
  49 => 'reduce_49_format_1',
  50 => 'reduce_50_calc_1',
  51 => 'reduce_51_calc_2',
  52 => 'reduce_52_calc_3',
  53 => 'reduce_53_calc_4',
  54 => 'reduce_54_calc_5',
  55 => 'reduce_55_calc_6',
  56 => 'reduce_56_calc_7',
  57 => 'reduce_57_start_1',
);
var $a = array (
  0 => 
  array (
    'symbol' => 'stmt',
    'len' => 1,
    'replace' => true,
  ),
  1 => 
  array (
    'symbol' => 'stmt',
    'len' => 3,
    'replace' => true,
  ),
  2 => 
  array (
    'symbol' => 'flow',
    'len' => 1,
    'replace' => true,
  ),
  3 => 
  array (
    'symbol' => 'flow',
    'len' => 8,
    'replace' => true,
  ),
  4 => 
  array (
    'symbol' => 'flow',
    'len' => 6,
    'replace' => true,
  ),
  5 => 
  array (
    'symbol' => 'expression',
    'len' => 1,
    'replace' => true,
  ),
  6 => 
  array (
    'symbol' => 'resource',
    'len' => 4,
    'replace' => true,
  ),
  7 => 
  array (
    'symbol' => 'resource',
    'len' => 6,
    'replace' => true,
  ),
  8 => 
  array (
    'symbol' => 'resource',
    'len' => 1,
    'replace' => true,
  ),
  9 => 
  array (
    'symbol' => 'resource',
    'len' => 3,
    'replace' => true,
  ),
  10 => 
  array (
    'symbol' => 'resourceid',
    'len' => 3,
    'replace' => true,
  ),
  11 => 
  array (
    'symbol' => 'resourceid',
    'len' => 3,
    'replace' => true,
  ),
  12 => 
  array (
    'symbol' => 'resourceid',
    'len' => 3,
    'replace' => true,
  ),
  13 => 
  array (
    'symbol' => 'num',
    'len' => 3,
    'replace' => true,
  ),
  14 => 
  array (
    'symbol' => 'num',
    'len' => 0,
    'replace' => true,
  ),
  15 => 
  array (
    'symbol' => 'filterlist',
    'len' => 1,
    'replace' => true,
  ),
  16 => 
  array (
    'symbol' => 'filterlist',
    'len' => 3,
    'replace' => true,
  ),
  17 => 
  array (
    'symbol' => 'filterlist',
    'len' => 3,
    'replace' => true,
  ),
  18 => 
  array (
    'symbol' => 'filterlist',
    'len' => 3,
    'replace' => true,
  ),
  19 => 
  array (
    'symbol' => 'filter',
    'len' => 3,
    'replace' => true,
  ),
  20 => 
  array (
    'symbol' => 'filter',
    'len' => 3,
    'replace' => true,
  ),
  21 => 
  array (
    'symbol' => 'filter',
    'len' => 3,
    'replace' => true,
  ),
  22 => 
  array (
    'symbol' => 'filter',
    'len' => 3,
    'replace' => true,
  ),
  23 => 
  array (
    'symbol' => 'filter',
    'len' => 3,
    'replace' => true,
  ),
  24 => 
  array (
    'symbol' => 'filter',
    'len' => 3,
    'replace' => true,
  ),
  25 => 
  array (
    'symbol' => 'filter',
    'len' => 3,
    'replace' => true,
  ),
  26 => 
  array (
    'symbol' => 'filter',
    'len' => 3,
    'replace' => true,
  ),
  27 => 
  array (
    'symbol' => 'filter',
    'len' => 3,
    'replace' => true,
  ),
  28 => 
  array (
    'symbol' => 'filter',
    'len' => 3,
    'replace' => true,
  ),
  29 => 
  array (
    'symbol' => 'filter',
    'len' => 3,
    'replace' => true,
  ),
  30 => 
  array (
    'symbol' => 'filter',
    'len' => 3,
    'replace' => true,
  ),
  31 => 
  array (
    'symbol' => 'filter',
    'len' => 3,
    'replace' => true,
  ),
  32 => 
  array (
    'symbol' => 'filter',
    'len' => 8,
    'replace' => true,
  ),
  33 => 
  array (
    'symbol' => 'selector',
    'len' => 1,
    'replace' => true,
  ),
  34 => 
  array (
    'symbol' => 'selector',
    'len' => 3,
    'replace' => true,
  ),
  35 => 
  array (
    'symbol' => 'selectargument',
    'len' => 1,
    'replace' => true,
  ),
  36 => 
  array (
    'symbol' => 'selectargument',
    'len' => 2,
    'replace' => true,
  ),
  37 => 
  array (
    'symbol' => 'selectargument',
    'len' => 3,
    'replace' => true,
  ),
  38 => 
  array (
    'symbol' => 'selectargument',
    'len' => 4,
    'replace' => true,
  ),
  39 => 
  array (
    'symbol' => 'function',
    'len' => 4,
    'replace' => true,
  ),
  40 => 
  array (
    'symbol' => 'function',
    'len' => 8,
    'replace' => true,
  ),
  41 => 
  array (
    'symbol' => 'order',
    'len' => 1,
    'replace' => true,
  ),
  42 => 
  array (
    'symbol' => 'order',
    'len' => 1,
    'replace' => true,
  ),
  43 => 
  array (
    'symbol' => 'argument',
    'len' => 1,
    'replace' => true,
  ),
  44 => 
  array (
    'symbol' => 'argument',
    'len' => 1,
    'replace' => true,
  ),
  45 => 
  array (
    'symbol' => 'argument',
    'len' => 1,
    'replace' => true,
  ),
  46 => 
  array (
    'symbol' => 'argument',
    'len' => 1,
    'replace' => true,
  ),
  47 => 
  array (
    'symbol' => 'argument',
    'len' => 1,
    'replace' => true,
  ),
  48 => 
  array (
    'symbol' => 'link',
    'len' => 5,
    'replace' => true,
  ),
  49 => 
  array (
    'symbol' => 'format',
    'len' => 1,
    'replace' => true,
  ),
  50 => 
  array (
    'symbol' => 'calc',
    'len' => 1,
    'replace' => true,
  ),
  51 => 
  array (
    'symbol' => 'calc',
    'len' => 3,
    'replace' => true,
  ),
  52 => 
  array (
    'symbol' => 'calc',
    'len' => 3,
    'replace' => true,
  ),
  53 => 
  array (
    'symbol' => 'calc',
    'len' => 3,
    'replace' => true,
  ),
  54 => 
  array (
    'symbol' => 'calc',
    'len' => 3,
    'replace' => true,
  ),
  55 => 
  array (
    'symbol' => 'calc',
    'len' => 3,
    'replace' => true,
  ),
  56 => 
  array (
    'symbol' => 'calc',
    'len' => 3,
    'replace' => true,
  ),
  57 => 
  array (
    'symbol' => '\'start\'',
    'len' => 1,
    'replace' => true,
  ),
);
}