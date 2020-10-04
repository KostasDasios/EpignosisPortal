<?php
    class Data extends Helper
    {
    	public function getDbDateTimeFormat($date) {
    		return date("Y-m-d H:i:s", strtotime($date));
    	}
    }
?>