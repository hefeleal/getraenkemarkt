<?php
    $ALLOW_HELPER = true;
    require_once("helpers.php");
    
    $config = parse_ini_file("data/config.ini");

    $latest_products = get_latest_items("data/products.csv");

    $handle = fopen("data/order.csv", "r");
    $orders = array();
    if($handle){
        fgets($handle);
        while(($arr = fgetcsv($handle)) !== false){
            $arr[2] = resolve_pfand($arr[2]);
            $arr[3] = resolve_order($arr[3]);
            $arr[4] = floatval($arr[4]);
            array_push($orders, $arr);
        }
        fclose($handle);
    }
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>Getränkemarkt - Übersicht</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/dataTables.bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <script src="js/jquery-1.11.3.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/jquery.dataTables.min.js"></script>
    <script src="js/dataTables.bootstrap.min.js"></script>
</head>
<?php
    echo "<body style='background-color: " . $config["color"] . ";'>";
    echo "<div class='jumbotron' style='background-color: " . $config["color"] . ";' >";
?>
<div class='container'>
    <h1>GETRÄNKEMARKT - Übersicht</h1>
    <h6 style="text-align: center;"><a href="index.php">zurück</a></h6>
</div>
</div>
<div class="container" style="padding-top: 0;">
    <div class="panel panel-default">
        <div class="panel-heading">Letzte Käufe</div>
        <table class="table table-hover hyphenate">
            <thead><tr><th style='width: 15%;'>Zeit</th><th style='width: 15%;'>Küche</th><th style='width: 20%;'>Pfand</th><th style='width: 35%;'>Was</th><th style='width: 15%;'>Betrag</th></tr></thead>
        </table>
        <div class="pre-scrollable">
            <table class="table table-hover hyphenate"><tbody>
            <?php
                for($i = count($orders)-1; $i >= 0; $i--){
                    $date_parts = explode(" ", $orders[$i][0]);
                    $curr = date("d.m.Y");
                    if($date_parts[0] == $curr){
                        $date = substr($date_parts[1], 0, -3);
                    }
                    else{
                        $date = substr($date_parts[0], 0, -4) . " " . substr($date_parts[1], 0, -3);
                    }
                    echo "<tr><td style='width: 15%;'>".$date."</td><td style='width: 15%;'>".$orders[$i][1]."</td><td style='width: 20%;'>".pfand_to_str($orders[$i][2])."</td><td style='width: 35%;'>".order_to_str($orders[$i][3])."</td><td style='width: 15%;'>".number_format($orders[$i][4], 2)."€</td></tr>";
                }

                function pfand_to_str($p){
                    $ret = "";
                    foreach ($p as $p_part){
                        $ret .= $p_part[0] . "&nbsp;*&nbsp;" . number_format($p_part[1], 2) . "€<br />";
                    }
                    return substr($ret, 0, -6);
                }

                function order_to_str($o){
                    global $latest_products;
                    $ret = "";
                    foreach ($o as $o_part){
                        $kasten = "";
                        if($o_part[1] == 1){
                            $kasten = "1 Kasten";
                        }
                        else if($o_part[1] > 1){
                            $kasten = $o_part[1] . " Kästen";
                        }
                        $bottles = "";
                        if($o_part[2] != 0){
                            if($o_part[1] > 0){
                                $bottles = ", ";
                            }
                            $bottles .= $o_part[2] . " Flasche";
                            $bottles .= abs($o_part[2]) == 1 ? "" : "n";
                        }
                        $ret .= $kasten . $bottles . " " . $latest_products[$o_part[0]][3] . "<br />";
                    }
                    return substr($ret, 0, -6);
                }
            ?>
            </tbody></table>
        </div>
    </div>
    <div class="panel panel-default">
        <div class="panel-heading">Scoreboard</div>
        <div style="text-align:center; margin-top: 10px;" data-toggle="buttons">
            <b>SoSe 2017</b>
            <div class="btn-group selectSemester">
                <label class="btn btn-default" data-toggle="tooltip" data-placement="top" title="24.04.2017&nbsp;-&nbsp;15.10.2017">
                    <input name="selectSemester" autocomplete="off" type="radio" value="24.04.2017-15.10.2017" />Komplett
                </label><label class="btn btn-default" data-toggle="tooltip" data-placement="top" title="24.04.2017&nbsp;-&nbsp;30.07.2017">
                    <input name="selectSemester" autocomplete="off" type="radio" value="24.04.2017-30.07.2017" />Vorlesungszeit
                </label><label class="btn btn-default" data-toggle="tooltip" data-placement="top" title="31.07.2017&nbsp;-&nbsp;15.10.2017">
                    <input name="selectSemester" autocomplete="off" type="radio" value="31.07.2017-15.10.2017" />Vorlesungsfrei
                </label>
            </div>
            <br /><br />
            <b>WiSe 17/18</b>
            <div class="btn-group selectSemester">
                <label class="btn btn-default" data-toggle="tooltip" data-placement="top" title="16.10.2017&nbsp;-&nbsp;08.04.2018">
                    <input name="selectSemester" autocomplete="off" type="radio" value="16.10.2017-08.04.2018" />Komplett
                </label><label class="btn btn-default" data-toggle="tooltip" data-placement="top" title="16.10.2017&nbsp;-&nbsp;11.02.2018">
                    <input name="selectSemester" autocomplete="off" type="radio" value="16.10.2017-11.02.2018" />Vorlesungszeit
                </label><label class="btn btn-default" data-toggle="tooltip" data-placement="top" title="12.02.2018&nbsp;-&nbsp;08.04.2018">
                    <input name="selectSemester" autocomplete="off" type="radio" value="12.02.2018-08.04.2018" />Vorlesungsfrei
                </label>
            </div>
            <br /><br />
            <b>SoSe 2018</b>
            <div class="btn-group selectSemester">
                <label class="btn btn-default" data-toggle="tooltip" data-placement="top" title="09.04.2018&nbsp;-&nbsp;14.10.2018">
                    <input name="selectSemester" autocomplete="off" type="radio" value="09.04.2018-14.10.2018" />Komplett
                </label><label class="btn btn-default" data-toggle="tooltip" data-placement="top" title="09.04.2018&nbsp;-&nbsp;14.07.2018">
                    <input name="selectSemester" autocomplete="off" type="radio" value="09.04.2018-14.07.2018" />Vorlesungszeit
                </label><label class="btn btn-default" data-toggle="tooltip" data-placement="top" title="15.07.2018&nbsp;-&nbsp;14.10.2018">
                    <input name="selectSemester" autocomplete="off" type="radio" value="15.07.2018-14.10.2018" />Vorlesungsfrei
                </label>
            </div>
            <br /><br />
            <b>WiSe 18/19</b>
            <div class="btn-group selectSemester">
                <label class="btn btn-default" data-toggle="tooltip" data-placement="top" title="15.10.2018&nbsp;-&nbsp;22.04.2019">
                    <input name="selectSemester" autocomplete="off" type="radio" value="15.10.2018-22.04.2019" />Komplett
                </label><label class="btn btn-default" data-toggle="tooltip" data-placement="top" title="15.10.2018&nbsp;-&nbsp;09.02.2019">
                    <input name="selectSemester" autocomplete="off" type="radio" value="15.10.2018-09.02.2019" />Vorlesungszeit
                </label><label class="btn btn-default" data-toggle="tooltip" data-placement="top" title="10.02.2019&nbsp;-&nbsp;22.04.2019">
                    <input name="selectSemester" autocomplete="off" type="radio" value="10.02.2019-22.04.2019" />Vorlesungsfrei
                </label>
            </div>
            <br /><br />
            <b>SoSe 2019</b>
            <div class="btn-group selectSemester">
                <label class="btn btn-default" data-toggle="tooltip" data-placement="top" title="23.04.2019&nbsp;-&nbsp;13.10.2019">
                    <input name="selectSemester" autocomplete="off" type="radio" value="23.04.2019-13.10.2019" />Komplett
                </label><label class="btn btn-default" data-toggle="tooltip" data-placement="top" title="23.04.2019&nbsp;-&nbsp;27.07.2019">
                    <input name="selectSemester" autocomplete="off" type="radio" value="23.04.2019-27.07.2019" />Vorlesungszeit
                </label><label class="btn btn-default" data-toggle="tooltip" data-placement="top" title="28.07.2019&nbsp;-&nbsp;13.10.2019">
                    <input name="selectSemester" autocomplete="off" type="radio" value="28.07.2019-13.10.2019" />Vorlesungsfrei
                </label>
            </div>
            <br /><br />
            <div class="btn-group selectSemester">
                <label class="btn btn-default active">
                    <input name="selectSemester" autocomplete="off" type="radio" value="01.01.1900-31.12.2100" />Alle
                </label>
            </div>
        </div>
        <table id="scoreboard" class="table table-hover hyphenate">
            <thead><tr><th>Kü&shy;che</th><th>Bier</th><th>Rad&shy;ler</th><th>Rest</th><th>Pfand</th><th>Aus&shy;ga&shy;ben</th><th>Ein&shy;käu&shy;fe</th></tr></thead>
            <tfoot id="scoreboardFooter"></tfoot>
        </table>
    </div>
</div>
<script type="text/javascript">
<?php
    echo "var allOrders = " . json_encode($orders) . ";";
    echo "var latestProducts = " . json_encode($latest_products) . ";";
?>

function toJsDate(str){
    var datePart = str.split(' ');
    var timePart = datePart[1].split(':');
    datePart = datePart[0].split('.');
    var result = new Date(datePart[2], datePart[1]-1, datePart[0], timePart[0], timePart[1], timePart[2]);
    result.setTime(result.getTime() - result.getTimezoneOffset() * 60 * 1000);
    return result;
}

function resortTable(start, end){
    var sSplit = start.split(".");
    var startDate = new Date(sSplit[2], sSplit[1]-1, sSplit[0], 0, 0, 0);
    startDate.setTime(startDate.getTime() - startDate.getTimezoneOffset() * 60 * 1000);
    var eSplit = end.split(".");
    var endDate = new Date(eSplit[2], eSplit[1]-1, eSplit[0], 23, 59, 59);
    endDate.setTime(endDate.getTime() - endDate.getTimezoneOffset() * 60 * 1000);
    var scoreboard = {};
    allOrders.forEach(function(o){
        var d = toJsDate(o[0]);
        if(d > startDate && d < endDate){
            if(scoreboard[o[1]] === undefined){
                scoreboard[o[1]] = [0, 0, 0, 0, 0, 0, 0, 0, 0];
            }
            o[3].forEach(function(oPart){
                if(latestProducts[oPart[0]][10] == "bier"){
                    scoreboard[o[1]][0] += oPart[1];
                    scoreboard[o[1]][1] += oPart[2];
                }
                else if(latestProducts[oPart[0]][10] == "radler"){
                    scoreboard[o[1]][2] += oPart[1];
                    scoreboard[o[1]][3] += oPart[2];
                }
                else if(latestProducts[oPart[0]][10] == "rest"){
                    scoreboard[o[1]][4] += oPart[1];
                    scoreboard[o[1]][5] += oPart[2];
                }
            });
            o[2].forEach(function(pPart){
                scoreboard[o[1]][6] += pPart[0] * pPart[1];
            });
            scoreboard[o[1]][7] += o[4];
            scoreboard[o[1]][8] += 1;
        }
    });
    var table = $("#scoreboard").DataTable();
    table.clear();
    var totalScore = [0, 0, 0, 0, 0, 0, 0, 0, 0];
    $.each(scoreboard, function(kitchen, score){
        for(var i = 0; i < score.length; i++){
            totalScore[i] += score[i];
        }
        table.row.add([kitchen, score[0], score[2], score[4], score[6].toFixed(2)+"€", score[7].toFixed(2)+"€", score[8]]);
    });
    $("#scoreboardFooter").html("<tr><td>Gesamt</td><td>"+totalScore[0]+"</td><td>"+totalScore[2]+"</td><td>"+totalScore[4]+"</td><td>"+totalScore[6].toFixed(2)+"€</td><td>"+totalScore[7].toFixed(2)+"€</td><td>"+totalScore[8]+"</td></tr>");
    table.draw();
}

$(document).ready(function(){
    $("[data-toggle='tooltip']").tooltip();

    $("#scoreboard").DataTable({
        "paging": false,
        "info": false,
        "searching": false,
        "order": [[1, "desc"], [2, "desc"], [3, "desc"]],
        "columnDefs": [{
            "orderSequence": ["desc", "asc"],
            "targets": [1, 2, 3, 4, 5, 6]
        }]
    });
    $(".selectSemester :input").change(function(){
        var dates = this.value.split("-");
        resortTable(dates[0], dates[1]);
    });
    resortTable("01.01.1900", "31.12.2100");
});
</script>
</body>
</html>
