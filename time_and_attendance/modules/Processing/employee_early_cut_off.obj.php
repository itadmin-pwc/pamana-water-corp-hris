<?
/*
	Date Created	:	08032010
	Created By		:	Genarra Arong
*/


class earlyObj extends commonObj {
	public function updateEarlyCut($from, $to) {
        // echo $from . " - " . $to;
        // die();
        $qryTblInfo = "UPDATE tbltk_timesheet SET timeIn=shftTimeIn, timeOut=shftTimeOut WHERE tsDate between '{$from}' and '{$to}'";
		return $this->execQry($qryTblInfo);
    }
}

?>