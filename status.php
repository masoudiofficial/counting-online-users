<?php

#This project was developed by @masoudiofficial, and all the code in the status.php file is the result of his ideas and creativity.

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

if (isset($_POST["xreceivechats"]) && !empty($_POST["xreceivechats"]) && preg_match("/^[a-z]+$/", $_POST["xreceivechats"]) && $_POST["xreceivechats"] === "xtrue" && empty($_FILES)) {
    if (strtolower($_SERVER['HTTP_HOST']) === 'localhost' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST["xreceiverchat"]) && !empty($_POST["xreceiverchat"]) && preg_match("/^[a-z0-9]+$/", $_POST["xreceiverchat"]) && strlen($_POST["xreceiverchat"]) <= 32) {

            try {

                $xpersiandatetime = xpersiandatetime();

                $xupdate1 = $xconnection->prepare("UPDATE userstable SET chatsstatus=CONCAT(?, chatsstatus) WHERE username=?");
                $xupdate1->execute([substr(str_shuffle('kF4dXHVenfSaq5KAyZcuBmUY1PT78pGtLl0sirhCJEMNjwQWDx9zOIR2o3bv6g'), 0, 19) . '>' . $xpersiandatetime['datetime1'] . ',', $_POST['xreceiverchat']]);

                $xselect1 = $xconnection->prepare("SELECT chats, chatsstatus FROM userstable WHERE username=?");
                $xselect1->execute([$_POST['xreceiverchat']]);
                $xselect1_r = $xselect1->fetch(PDO::FETCH_ASSOC);

                if (!empty($xselect1_r['chats'])) {

                    $xtotal = 0;

                    if (!empty($xselect1_r['chatsstatus']) && strpos($xselect1_r['chatsstatus'], $xpersiandatetime['datetime3']) !== false) {

                        $xfiltered = [];
                        $xfiltered2 = [];
                        $xfiltered3 = '';
                        $xarray = explode(',', substr($xselect1_r['chatsstatus'], 0, -1));

                        $xupdate2 = $xconnection->prepare("UPDATE userstable SET chatsstatus=? WHERE username=?");

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
                                if ($xselect1_r['chatsstatus'] !== $xfiltered3) {
                                    $xupdate2->execute([$xfiltered3, $_POST['xreceiverchat']]);
                                }
                                break;
                            }
                        }

                        $xfiltered = array_flip($xfiltered);
                        $xtotal = count($xfiltered);
                    }

                    echo json_encode(array("xchats" => $xselect1_r['chats'], "xtotal" => $xtotal));
                }
            } catch (Throwable $e) {
                echo json_encode(array("xchats" => "Unfortunately, there is a problem !", "xtotal" => 0));
            } finally {
                $xconnection = null;
            }
        }
    }
}
?>
