<?php
/**
 * This class handles an XLS file
 *
 * @package core/strategies
 * @copyright (C) 2011 by iRail vzw/asbl
 * @license AGPLv3
 * @author Lieven Janssen
 * @author Jan Vansteenlandt
 */

namespace tdt\core\strategies;

use tdt\exceptions\TDTException;
use tdt\core\model\resources\AResourceStrategy;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use tdt\core\utility\Config;
use PHPExcel_IOFactory as IOFactory;
use tdt\core\model\ResourcesModel;


class XLS extends ATabularData {

    public function __construct(){
        parent::__construct();
        $this->tmp_dir = __DIR__ . "/../tmp";
    }

    private $tmp_dir;

    public function documentCreateParameters(){
        $this->parameters["uri"] = array(
            "description" => "The path to the excel sheet (can be a url as well).",
            "required" => true,
        );

        $this->parameters["sheet"] = array(
            "description" => "The sheet name of the excel",
            "required" => true,
        );

        $this->parameters["named_range"] = array(
            "description" => "The named range of the excel",
            "required" => false,
        );

        $this->parameters["cell_range"] = array(
            "description" => "Range of cells (i.e. A1:B10)",
            "required" => false,
        );

        $this->parameters["PK"] = array(
            "description" => "The primary key for each row.",
            "required" => false,
        );

        $this->parameters["has_header_row"] = array(
            "description" => "If the XLS file contains a header row with the column name, pass 1 as value, if not pass 0. Default value is 1.",
            "required" => false,
            "defaultValue" => true,
        );

        $this->parameters["start_row"] = array(
            "description" => "The number of the row (rows start at number 1) at which the actual data starts; i.e. if the first two lines are comment lines, your start_row should be 3. Default is 1.",
            "required" => false,
            "defaultValue" => 1,
        );


        return $this->parameters;
    }

    public function documentReadParameters(){
        return array();
    }

    protected function throwTDTException($message){
        $exception_config = array();
        $exception_config["log_dir"] = Config::get("general", "logging", "path");
        $exception_config["url"] = Config::get("general", "hostname") . Config::get("general", "subdir") . "error";
        throw new TDTException(452, array($message), $exception_config);
    }

    protected function isValid($package_id,$generic_resource_id) {

        if (!isset($this->columns)) {
            $this->columns = array();
        }

        if(!isset($this->column_aliases)){
            $this->column_aliases = array();
        }

        if (!isset($this->PK) && isset($this->pk)) {
            $this->PK = $this->pk;

        }else if(empty($this->PK)){
            $this->PK = "";
        }

        if (!isset($this->start_row)) {
            $this->start_row = 1;
        }

        if (!isset($this->has_header_row)) {
            $this->has_header_row = 1;
        }

        $uri = $this->uri;
        $sheet = $this->sheet;
        $columns = $this->columns;

        /**
         * if no header row is given, then the columns that are being passed should be
         * int => something, int => something
         * if a header row is given however in the csv file, then we're going to extract those
         * header fields and put them in our back-end as well.
         */

        if ($this->has_header_row == "0") {
            // no header row ? then columns must be passed
            if(empty($this->columns)){
                $this->throwTDTException("Your array of columns must be an index => string hash array. Since no header row is specified in the resource CSV file.");
            }

            foreach ($this->columns as $index => $value) {
                if (!is_numeric($index)) {
                    $this->throwTDTException("Your array of columns must be an index => string hash array.");
                }
            }

        } else {

            // if no column aliases have been passed, then fill the columns variable
            if(empty($this->columns)){
                if (!is_dir($this->tmp_dir)) {
                    mkdir($this->tmp_dir);
                }

                $isUri = (substr($uri , 0, 4) == "http");
                if ($isUri) {
                    $tmpFile = uniqid();

                    file_put_contents($this->tmp_dir. "/" . $tmpFile, file_get_contents($uri));
                    $objPHPExcel = $this->loadExcel($this->tmp_dir ."/" . $tmpFile,$this->getFileExtension($uri),$sheet);
                } else {
                    $objPHPExcel = $this->loadExcel($uri,$this->getFileExtension($uri),$sheet);
                }

                $worksheet = $objPHPExcel->getSheetByName($sheet);

                if(is_null($worksheet)){
                    $this->throwTDTException("The sheet with name, $sheet, has not been found in the Excel file.");
                }

                if (!isset($this->named_range) && !isset($this->cell_range)) {
                    foreach ($worksheet->getRowIterator() as $row) {
                        $rowIndex = $row->getRowIndex();
                        $dataIndex = 0;
                        if ($rowIndex == $this->start_row) {
                            $cellIterator = $row->getCellIterator();
                            $cellIterator->setIterateOnlyExistingCells(false);
                            foreach ($cellIterator as $cell) {
                                if($cell->getCalculatedValue() != ""){
                                    $this->columns[$dataIndex] = $cell->getCalculatedValue();
                                }
                                $dataIndex++;
                            }
                        }
                    }
                } else {
                    if(isset($this->named_range)) {
                        $range = $worksheet->namedRangeToArray($this->named_range);
                    }
                    if(isset($this->cell_range)) {
                        $range = $worksheet->rangeToArray($this->cell_range);
                    }
                    $rowIndex = 1;
                    foreach ($range as $row) {
                        $dataIndex = 0;
                        if ($rowIndex == $this->start_row) {
                            foreach ($row as $cell) {
                                $this->columns[$dataIndex] = $cell;
                            }
                            $dataIndex++;
                        }
                        $rowIndex += 1;
                    }
                }
                $objPHPExcel->disconnectWorksheets();
                unset($objPHPExcel);
                if ($isUri) {
                    unlink($this->tmp_dir . "/" . $tmpFile);
                }
            }
        }
        return true;
    }

    public function read(&$configObject,$package,$resource){

        parent::read($configObject,$package,$resource);

        // Get the necessary parameters to read an Excel file.
        $uri = $configObject->uri;
        $sheet = $configObject->sheet;
        $has_header_row = $configObject->has_header_row;
        $start_row = $configObject->start_row;

        if($has_header_row){
            $start_row++;
        }                

        $PK = $configObject->PK;

        $columns = $configObject->columns;
        $column_aliases = $configObject->column_aliases;

        $resultobject = new \stdClass();
        $arrayOfRowObjects = array();
        $row = 0;
        $hits = 0;

        // Set the parameters for paging
        $total_rows = 0;
        $limit = $this->limit;
        $offset = $this->offset;

        $model = ResourcesModel::getInstance();
        $column_infos = $model->getColumnsFromResource($this->package,$this->resource);
        $aliases = array();

        foreach($column_infos as $column_info){
            $aliases[$column_info["column_name"]] = $column_info["column_name_alias"];
        }

        if (!is_dir($this->tmp_dir)) {
            mkdir($this->tmp_dir);
        }

        try {
            $isUri = (substr($uri , 0, 4) == "http");
            if ($isUri) {
                // We cannot stream the XLS file, so if it's on an url, we have to store in the temp folder.
                $tmpFile = uniqid();
                file_put_contents($this->tmp_dir . "/" . $tmpFile, file_get_contents($uri));
                $objPHPExcel = $this->loadExcel($this->tmp_dir . "/" . $tmpFile,$this->getFileExtension($uri),$sheet);

            } else {
                $objPHPExcel = $this->loadExcel($uri,$this->getFileExtension($uri),$sheet);
            }

            $worksheet = $objPHPExcel->getSheetByName($sheet);

            if (($configObject->named_range == "" || $configObject->named_range == "0") && ($configObject->cell_range == "" || $configObject->cell_range == "0")) {
                foreach ($worksheet->getRowIterator() as $row) {
                    $rowIndex = $row->getRowIndex();
                    if ($rowIndex >= $start_row) {
                        if($offset <= $hits && $offset + $limit > $hits){
                            $cellIterator = $row->getCellIterator();
                            $cellIterator->setIterateOnlyExistingCells(false);

                            $alias_copy = $aliases;
                            $rowobject = new \stdClass();                            

                            foreach ($cellIterator as $cell) {
                                $columnIndex = $cell->columnIndexFromString($cell->getColumn());
                                if (!is_null($cell) && isset($column_infos[$columnIndex-1]) ) {
                                    // format the column name as we normally format column names
                                    $c = array_shift($alias_copy);
                                    $c = trim($c);
                                    $c = preg_replace('/\s+/', '_', $c);
                                    $c = strtolower($c);

                                    if(in_array($c,$aliases)){
                                        $rowobject->$aliases[$c] = $cell->getCalculatedValue();
                                    }
                                }
                            }
                            if($PK == "") {
                                array_push($arrayOfRowObjects,$rowobject);
                            } else {
                                if(!isset($arrayOfRowObjects[$rowobject->$PK]) && $rowobject->$PK != ""){
                                    $arrayOfRowObjects[$rowobject->$PK] = $rowobject;
                                }elseif(isset($arrayOfRowObjects[$rowobject->$PK])){
                                    $log = new Logger('XLS');
                                    $log->pushHandler(new StreamHandler(Config::get("general", "logging", "path") . "/log_" . date('Y-m-d') . ".txt", Logger::ALERT));
                                    $log->addAlert("The primary key $PK has been used already for another record!");
                                }else{
                                    $log = new Logger('XLS');
                                    $log->pushHandler(new StreamHandler(Config::get("general", "logging", "path") . "/log_" . date('Y-m-d') . ".txt", Logger::ALERT));
                                    $log->addAlert("The primary key $PK is empty.");
                                }
                            }
                        }
                        $hits++;
                    }
                    $total_rows++;
                }
            } 

            $total_rows -= $start_row - 1;
            
            // Paging.
            if($offset + $limit < $hits){
                $page = $offset/$limit;
                $page = round($page,0,PHP_ROUND_HALF_DOWN);
                if($page==0){
                    $page = 1;
                }
                $this->setLinkHeader($page + 1,$limit,"next");

                $last_page = ceil(round($total_rows / $this->limit,1));
                if($last_page > $this->page+1){
                    $this->setLinkHeader($last_page,$this->page_size, "last");
                }
            }

            if($offset > 0 && $hits >0){
                $page = $offset/$limit;
                $page = round($page,0,PHP_ROUND_HALF_DOWN);
                $page--;
                if($page <= 0){                   
                    $page = 1;
                }

                $this->setLinkHeader($page,$limit,"previous");
            }
            
            $objPHPExcel->disconnectWorksheets();
            unset($objPHPExcel);
            if ($isUri) {
                unlink($this->tmp_dir . "/" . $tmpFile);
            }

            return $arrayOfRowObjects;
        } catch( Exception $ex) {
            throw new CouldNotGetDataTDTException( $uri );
        }
    }

    private function getFileExtension($fileName){
        return strtolower(substr(strrchr($fileName,'.'),1));
    }

    private function loadExcel($xlsFile,$type,$sheet) {

        $dummy = new \PHPExcel();

        if($type == "xls") {
            $objReader = IOFactory::createReader('Excel5');
        }else if($type == "xlsx") {
            $objReader = IOFactory::createReader('Excel2007');
        }else{
            $this->throwTDTException("Wrong datasource, accepted datasources are .xls or .xlsx files.");
        }

        $objReader->setReadDataOnly(true);
        $objReader->setLoadSheetsOnly($sheet);
        return $objReader->load($xlsFile);
    }
}