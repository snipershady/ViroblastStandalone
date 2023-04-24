<?php

#######################################################################################
# ViroBLAST
# sequence.php
# Copyright © University of Washington. All rights reserved.
# Written by Wenjie Deng in the Department of Microbiology at University of Washington.
#######################################################################################
require_once __DIR__ . '/bootstrap.php';
$epti = new \TypeIdentifier\Service\EffectivePrimitiveTypeIdentifierService();
require_once __DIR__ . '/template/header.php';
require_once __DIR__ . '/template/navbar.php';

set_time_limit(600);
$jobid = $epti->getTypedValue(filter_input(INPUT_GET, "jobid", FILTER_UNSAFE_RAW), true);
$target = filter_input(INPUT_POST, "target", FILTER_UNSAFE_RAW);
$dldseq = filter_input(INPUT_POST, "dldseq", FILTER_UNSAFE_RAW);
$seqtype = filter_input(INPUT_POST, "seqtype", FILTER_UNSAFE_RAW);
$downloadFile = $jobid . ".download.fas";
$blastFiles = [];

$fp_log = fopen("$dataPath/$jobid.log", "r");
if ($fp_log === false) {
    header("location: index.php");
    exit;
}

while (!feof($fp_log)) {
    $line = fgets($fp_log);
    $line = rtrim($line);
    if (preg_match("/Program: (\S+)/", $line, $match)) {
        $program = $match[1];
    } elseif (preg_match("/Blast against:\s+(.*)$/", $line, $match)) {
        $blastagainst = $match[1];
        if (preg_match("/\s+/", $blastagainst, $match)) {
            $blastFiles = preg_split("/\s+/", $blastagainst);
        } else {
            array_push($blastFiles, $blastagainst);
        }
    }
}
fclose($fp_log);

echo "<b><a href=./download.php?ID=$downloadFile><img src=image/download.png></a></b><br><br>";

if ($dldseq) {
    $fp_parse = fopen("$dataPath/$jobid.download.txt", "r") or die("Cannot open $jobid.download.txt to read");
    $target = [];
    while (!feof($fp_parse)) {
        $record = fgets($fp_parse);
        $record = rtrim($record);
        if (!$record) {
            continue;
        }
        array_push($target, $record);
    }
    fclose($fp_parse);
}
$sbjcts = [];
$querysbjcts = [];
for ($i = 0; $i < (is_countable($target) ? count($target) : 0); $i++) {
    [$page, $query, $sbjct] = preg_split("/\t/", (string) $target[$i]);
    $sbjcts[$sbjct] = 1;
    $querysbjct = $query . "-" . $sbjct;
    $querysbjcts[$querysbjct] = 1;
}

$fp_dld = fopen("$dataPath/$jobid.download.fas", "w", true) or die("couldn't open download.fas to write");

if ($seqtype == "entire") {
    $sbjctSeq = [];
    $sbjctTitle = [];
    $flag = 0;
    for ($i = 0; $i < (is_countable($blastFiles) ? count($blastFiles) : 0); $i++) {
        $file = $blastFiles[$i];
        $fp = fopen($file, "r") or die("couldn't open $file");
        while (!feof($fp)) {
            $line = fgets($fp);
            if (preg_match("/^>(.*?)[,;\s+]/", $line, $match) || preg_match("/^>(\S+)/", $line, $match)) {
                $seqName = $match[1];
                if (array_key_exists($seqName, $sbjcts)) {
                    $flag = 1;
                    $sbjctTitle[$seqName] = $line;
                } else {
                    $flag = 0;
                }
            } elseif ($flag) {
                $line = preg_replace("/[\-\s]/", "", $line);
                $line = strtoupper($line);
                if (!array_key_exists($seqName, $sbjctSeq)) {
                    $sbjctSeq[$seqName] = "";
                }
                $sbjctSeq[$seqName] .= $line;
            }
        }
    }
    foreach ($sbjcts as $name => $value) {
        $seqName = $sbjctTitle[$name];
        $seq = $sbjctSeq[$name];
        fwrite($fp_dld, "$seqName");
        while ($seq) {
            $first = substr($seq, 0, 80);
            $seq = substr($seq, 80);
            fwrite($fp_dld, "$first\n");
        }
    }
} elseif ($seqtype == "mapping") {
    $accName = [];
    $querySeq = [];
    $sbjctSeq = [];
    $sbjctOri = [];
    $flag = 0;
    $fp_st = fopen("$dataPath/$jobid.out", "r") or die("couldn't open $jobid.out.");
    while (!feof($fp_st)) {
        $line = fgets($fp_st);
        $line = rtrim($line);
        if (preg_match("/^<b>Query=<\/b>\s+(.*?)[,;\s+]/", $line, $match) || preg_match("/^<b>Query=<\/b>\s+(\S+)/", $line, $match)) {
            $query = $match[1];
        } elseif (preg_match("/^><a(.*?)<\/a>\s+(.*?)([,;\s+].*)/", $line, $match) || preg_match("/^><a(.*?)<\/a>\s+(\S+)/", $line, $match)) {
            $acc = $name = $match[2];
            if ($match[3]) {
                $name = $match[2] . $match[3];
            }
            $queryacc = $query . "-" . $acc;
            if (array_key_exists($queryacc, $querysbjcts)) {
                $flag = 1;
            } else {
                $flag = 0;
            }
        } elseif ($flag == 1) {
            if (preg_match("/Length=/", $line, $match)) {
                $flag = 2;
                $accName[$acc] = $name;
            } else {
                $name .= " $line";
            }
        } elseif ($flag == 2) {
            if (preg_match("/Score =/", $line, $match)) {
                $qstart = $qend = $sstart = $send = 0;
            }
            if (preg_match("/Strand=(.*)/", $line, $match)) {
                $minusflag = $match[1];
            }
            if (preg_match("/^Query\s+(\d+)\s+(.*)\s+(\d+)$/", $line, $match)) {
                if ($qstart == 0) {
                    $qstart = $match[1];
                }
                $qseq = $match[2];
                $qseq = preg_replace("/<(.*?)>/", "", $qseq);
                $qseq = preg_replace("/-+/", "", $qseq);
                $qseq = preg_replace("/\s+/", "", $qseq);
            }
            if (preg_match("/^Sbjct\s+(\d+)\s+(.*)\s+(\d+)$/", $line, $match)) {
                if ($sstart == 0) {
                    $sstart = $match[1];
                    $sbjctOri[$query][$acc][$qstart][$sstart] = $minusflag;
                }
                $sseq = $match[2];
                $sseq = preg_replace("/<(.*?)>/", "", $sseq);
                $sseq = preg_replace("/-+/", "", $sseq);
                $sseq = preg_replace("/\s+/", "", $sseq);
                $querySeq[$query][$acc][$qstart][$sstart] .= $qseq;
                $sbjctSeq[$query][$acc][$qstart][$sstart] .= $sseq;
            }
        }
    }
    fclose($fp_st);

    foreach ($sbjctSeq as $query => $qarray) {
        foreach ($qarray as $acc => $aarray) {
            foreach ($aarray as $qstart => $qsarray) {
                foreach ($qsarray as $sstart => $sbjctseq) {
                    $queryseq = $querySeq[$query][$acc][$qstart][$sstart];
                    $mflag = $sbjctOri[$query][$acc][$qstart][$sstart];
                    $qlen = strlen($queryseq);
                    $slen = strlen($sbjctseq);
                    $qend = $qstart + $qlen - 1;
                    if ($mflag == "Plus/Minus") {
                        $send = $sstart - $slen + 1;
                    } elseif ($mflag == "Plus/Plus") {
                        $send = $sstart + $slen - 1;
                    }
                    fwrite($fp_dld, ">" . $accName[$acc] . " (Subject: $acc, $sstart..$send; Query: $query, $qstart..$qend)\n");
                    while ($sbjctseq) {
                        $first = substr($sbjctseq, 0, 80);
                        $sbjctseq = substr($sbjctseq, 80);
                        fwrite($fp_dld, "$first\n");
                    }
                }
            }
        }
    }
}
fclose($fp_dld);

$fp = fopen("$dataPath/$jobid.download.fas", "r") or die("couldn't open $jobid.download.fas.");
while (!feof($fp)) {
    $line = fgets($fp);
    $line = rtrim($line);
    echo "$line<br>";
}
fclose($fp);
?>

<?php

require_once __DIR__ . '/template/footer.php';
