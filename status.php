<?php

#This project was developed by @masoudiofficial, all the code in this file is the result of his ideas and creativity.

include_once './config.php';

function is_persian_leap_year($year) {
    $mod = $year % 33;
    return in_array($mod, [1, 5, 9, 13, 17, 22, 26, 30]);
}

function xpersiandatetime() {
    $modifiers = [
        'datetime1' => 'now',
        'datetime2' => '-1 second',
        'datetime3' => '-3 second',
        'datetime4' => '-4 second'
    ];
    $results = [];
    foreach ($modifiers as $key => $modifier) {
        $origin = new DateTime('622-03-21 00:00:00', new DateTimeZone('Asia/Tehran'));
        $now = new DateTime('now', new DateTimeZone('Asia/Tehran'));
        if ($modifier !== 'now') {
            $now->modify($modifier);
        }
        $diff = $now->diff($origin);
        $persianYear = 1;
        $days = $diff->days;
        while ($days >= (is_persian_leap_year($persianYear) ? 366 : 365)) {
            $days -= is_persian_leap_year($persianYear) ? 366 : 365;
            $persianYear++;
        }
        $persianMonths = [31, 31, 31, 31, 31, 31, 30, 30, 30, 30, 30, is_persian_leap_year($persianYear) ? 30 : 29];
        $persianMonth = 1;
        $persianDay = $days + 1;
        foreach ($persianMonths as $monthLength) {
            if ($persianDay <= $monthLength) {
                break;
            }
            $persianDay -= $monthLength;
            $persianMonth++;
        }
        $time = $now->format('H:i:s');
        $results[$key] = sprintf('%04d-%02d-%02d %s', $persianYear, $persianMonth, $persianDay, $time);
    }
    return $results;
}

if (isset($_POST['xreceivechats']) && !empty($_POST['xreceivechats']) && preg_match('/^[a-z]+$/', $_POST['xreceivechats']) && $_POST['xreceivechats'] === 'xtrue') {
    if (strtolower($_SERVER['HTTP_HOST']) === 'localhost' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        if (preg_match('/^[a-z0-9]+$/', $_POST['xreceiverchat']) && mb_strlen($_POST['xreceiverchat'], 'UTF-8') <= 32) {

            $xpersiandatetime = xpersiandatetime();

            $xupdate = $xconnection->prepare("UPDATE userstable SET chatsstatus=CONCAT(?, chatsstatus) WHERE username=?");
            $xupdate->execute([substr(str_shuffle('kF4dXHVenfSaq5KAyZcuBmUY1PT78pGtLl0sirhCJEMNjwQWDx9zOIR2o3bv6g'), 0, 19) . '>' . $xpersiandatetime['datetime1'] . ',', $_POST['xreceiverchat']]);

            $xselect = $xconnection->prepare("SELECT chats, chatsstatus FROM userstable WHERE username=?");
            $xselect->execute([$_POST['xreceiverchat']]);
            $xselect = $xselect->fetch(PDO::FETCH_ASSOC);

            $xchatsstatus = $xselect['chatsstatus'];
            $xchats = $xselect['chats'];

            if ($xchats !== '') {
                $xtotal = 0;
                if ($xchatsstatus !== '' && strpos($xchatsstatus, $xpersiandatetime['datetime3']) !== false) {
                    $xfiltered = [];
                    $xfiltered2 = [];
                    $xfiltered3 = '';
                    $xarray = explode(',', substr($xchatsstatus, 0, -1));
                    foreach ($xarray as $xnum) {
                        $xnum2 = explode('>', $xnum)[1];
                        if ($xnum2 >= $xpersiandatetime['datetime2']) {
                            $xfiltered2[] = $xnum;
                        } else if ($xnum2 < $xpersiandatetime['datetime2'] && $xnum2 > $xpersiandatetime['datetime3']) {
                            $xfiltered2[] = $xnum;
                            $xfiltered[] = $xnum;
                        } else if ($xnum2 <= $xpersiandatetime['datetime3'] && $xnum2 > $xpersiandatetime['datetime4']) {
                            $xfiltered2[] = $xnum;
                        } else {
                            $xfiltered3 = implode(',', $xfiltered2) . ',';
                            if ($xchatsstatus !== $xfiltered3) {
                                $xupdate = $xconnection->prepare("UPDATE userstable SET chatsstatus=? WHERE username=?");
                                $xupdate->execute([$xfiltered3, $_POST['xreceiverchat']]);
                            }
                            break;
                        }
                    }
                    $xfiltered = array_flip($xfiltered);
                    $xtotal = count($xfiltered);
                }
                $xresponse = array("xchats" => $xchats, "xtotal" => $xtotal);
                echo json_encode($xresponse);
            }
        }
    }
}
?>
