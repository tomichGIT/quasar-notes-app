<?php
namespace App\Service;

class TimeHelper {
    public function humanReadableTimeDiff(\DateTime $createdAt, $exact=false): string
    {
        $now = new \DateTime();
        $interval = $now->diff($createdAt);

        $years = $interval->y;
        $months = $interval->m;
        $days = $interval->d;
        $hours = $interval->h;
        $minutes = $interval->i;
        $seconds = $interval->s;

        $diffString = $exact?"Creada hace ":"Creada hace mas de ";
        if ($years > 0)     { $diffString .= $years . " año(s) ";       if(!$exact){ return $diffString; }        }
        if ($months > 0)    { $diffString .= $months . " mes(s) ";      if(!$exact){ return $diffString; }        }
        if ($days > 0)      { $diffString .= $days . " día(s) ";        if(!$exact){ return $diffString; }        }
        if ($hours > 0)     { $diffString .= $hours . " hora(s) ";      if(!$exact){ return $diffString; }        }
        if ($minutes > 0)   { $diffString .= $minutes . " minuto(s) ";  if(!$exact){ return $diffString; }        }
        if ($seconds > 0)   { $diffString .= $seconds . " segundo(s) "; if(!$exact){ return $diffString; }        }
        return $diffString;
    }
}
?>