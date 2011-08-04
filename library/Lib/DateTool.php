<?php
class Lib_DateTool {
	public static function date_diff($start, $end="NOW")
	{
        $sdate = strtotime($start);
        $edate = strtotime($end);

        $time = $edate - $sdate;
        
        if($time>=0 && $time<=59) {
                // Seconds
                $timeshift = $time.' seconds ';

        } elseif($time>=60 && $time<=3599) {
                // Minutes + Seconds
                $pmin = ($edate - $sdate) / 60;
                $premin = explode('.', $pmin);
                
                $presec = $pmin-$premin[0];
                $sec = $presec*60;
                
                $timeshift = $premin[0].' min '.round($sec,0).' sec ';

        } elseif($time>=3600 && $time<=86399) {
                // Hours + Minutes
                $phour = ($edate - $sdate) / 3600;
                $prehour = explode('.',$phour);
                
                $premin = $phour-$prehour[0];
                $min = explode('.',$premin*60);
                
                $presec = '0.'.$min[1];
                $sec = $presec*60;

                $timeshift = $prehour[0].' hrs '.$min[0].' min '.round($sec,0).' sec ';

        } elseif($time>=86400) {
                // Days + Hours + Minutes
                $pday = ($edate - $sdate) / 86400;
                $preday = explode('.',$pday);

                $phour = $pday-$preday[0];
                $prehour = explode('.',$phour*24); 

                $premin = ($phour*24)-$prehour[0];
                $min = explode('.',$premin*60);
                
                $presec = '0.'.$min[1];
                $sec = $presec*60;
                
                $timeshift = $preday[0].' days '.$prehour[0].' hrs '.$min[0].' min '.round($sec,0).' sec ';

        }
	    return $timeshift;
	}
	
	public static function age($naiss)  
	{
		list($annee, $mois, $jour) = split('[-.]', $naiss);
		$today['mois'] = date('n');
		$today['jour'] = date('j');
		$today['annee'] = date('Y');
		$annees = $today['annee'] - $annee;
		if ($today['mois'] <= $mois) {
			if ($mois == $today['mois']) {
				if ($jour > $today['jour'])
					$annees--;
      		}
   			else
     			$annees--;
    	}
  		return $annees;
	}
	
	public static function getMonthName($num){
		if ($num == 1)
			return 'janvier';
		if ($num == 2)
			return 'fevrier';
		if ($num == 3)
			return 'mars';
		if ($num == 4)
			return 'avril';
		if ($num == 5)
			return 'mai';
		if ($num == 6)
			return 'juin';
		if ($num == 7)
			return 'juillet';
		if ($num == 8)
			return 'aout';
		if ($num == 9)
			return 'septembre';
		if ($num == 10)
			return 'octobre';
		if ($num == 11)
			return 'novembre';	
		if ($num == 12)
			return 'decembre';	
	}
	
	public static function getSignFromDate($date) {
		list($year,$month,$day)=explode("-",$date);
	     if (($month==1 && $day>20) || ($month==2 && $day<20)) {
	          return "Verseau";
	     }
	     
	     if (($month==2 && $day>18 ) || ($month==3 && $day<21)){
	          return "Poisson";
	     }
	     
	     if (($month==3 && $day>20) || ($month==4 && $day<21)){
	          return "Bélier";
	     }
	     
	     if (($month==4 && $day>20) || ($month==5 && $day<22)){
	          return "Taureau";
	     }
	     
	     if (($month==5 && $day>21) || ($month==6 && $day<22)){
	          return "Gemeau";
	     }
	     
	     if (($month==6 && $day>21) || ($month==7 && $day<24)){
	          return "Cancer";
	     }
	     
	     if (($month==7 && $day>23) || ($month==8 && $day<24)){
	          return "Lion";
	     }
	     
	     if (($month==8 && $day>23) || ($month==9 && $day<24)){
	          return "Vierge";
	     }
	     
	     if (($month==9 && $day>23) || ($month==10 && $day<24)){
	          return "Balance";
	     }
	     
	     if (($month==10 && $day>23) || ($month==11 && $day<23)){
	          return "Scorpion";
	     }
	     
	     if (($month==11 && $day>22) || ($month==12 && $day<23)){
	          return "Sagittaire";
	     }
	     
	     if (($month==12 && $day>22) || ($month==1 && $day<21)){
	          return "Capricorne";
	     }
	}
	
	public static function formatSqlDate($date, $format = 'd-m-Y') {
		list($year, $month, $day) = explode('-', $date);
		$time = mktime(0, 0, 0, $month, $day, $year);
		return date($format, $time);			
	}
}