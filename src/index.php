<?php
    $ALLOW_HELPER = true;
    require_once("helpers.php");
    $config = parse_ini_file("data/config.ini");

    $current_date = new DateTime("now");

    $latest_products = get_latest_items("data/products.csv");
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>Getränkemarkt</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <script src="js/jquery-1.11.3.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
</head>
<?php
echo "<body style='background-color: " . $config["color"] . ";'>";
?>
<div id="pinModal" class="modal fade">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
        <p class="modal-title"><strong>Passwort eingeben</strong></p>
      </div>
      <div class="modal-body">
        <form id="modalForm">
            <div class="form-group">
                <input id="pinInputField" type="password" placeholder="Dein Passwort" autocomplete="off" class="form-control" value="" name="pin" />
            </div>
            <div class="checkbox">
                <label><input id="rememberCheckbox" type="checkbox" autocomplete="off" value="1" name="remember" /> auf diesem Gerät eingeloggt bleiben</label><br />
            </div>
            <input type="submit" class="btn btn-default" value="OK" />
        </form>
      </div>
    </div>
  </div>
</div>
<?php
    echo "<div class='jumbotron' style='background-color: " . $config["color"] . ";' >";
?>
<div class='container'>
    <h1>GETRÄNKEMARKT</h1>
</div>
</div>
<div class="container" style="padding-top: 0;">
    <div class="panel panel-default">
        <div class="panel-heading">Pfand</div>
        <table class="table table-hover hyphenate">
            <thead><tr><th>Art</th><th>Anzahl</th></tr></thead><tbody>
        <?php
            $pfand_kasten = array();
            $pfand_flasche = array();
            $pfand_traeger = array();
            foreach ($latest_products as $pr){
                if($pr[6] != 0 && !in_array($pr[6], $pfand_kasten)){
                    array_push($pfand_kasten, $pr[6]);
                }
                if($pr[7] != 0 && !in_array($pr[7], $pfand_flasche)){
                    array_push($pfand_flasche, $pr[7]);
                }
                if($pr[8] != 0 && !in_array($pr[8], $pfand_traeger)){
                    array_push($pfand_traeger, $pr[8]);
                }
            }
            $j = 0;
            $pfand = array_merge($pfand_kasten, $pfand_traeger, $pfand_flasche);
            foreach ($pfand as $p){
                $description = "Kasten";
                if($j >= count($pfand_kasten)+count($pfand_traeger)){
                    $description = "Flasche";
                }
                else if($j >= count($pfand_kasten)){
                    $description = "Träger";
                }
                echo "<tr><td><b>" . $description . " ".number_format($p, 2)."€</b></td>";
                echo "<td>";
                if($j >= count($pfand_kasten)+count($pfand_traeger)){
                    echo "<div class='input-group' style='width:130px;'>";
                    echo "<div class='input-group-btn'>";
                    echo "<a onclick='updatePfand(".$j.", false);' class='btn btn-default'><span class='glyphicon glyphicon-minus'></span></a>";
                    echo "</div>";
                    echo "<input id='pfandDisplay".$j."' class='form-control' type='text' autocomplete='off' value='0' >";
                    echo "<div class='input-group-btn'>";
                    echo "<a onclick='updatePfand(".$j.", true);' class='btn btn-default'><span class='glyphicon glyphicon-plus'></span></a>";
                    echo "</div>";
                    echo "</div>";
                }
                else{
                    echo "<a style='text-decoration:none;' onclick='updatePfand(".$j.", false);' class='btn btn-default'><span class='glyphicon glyphicon-minus'></span></a>";
                    echo "<span id='pfandDisplay".$j."' style='margin: 0 21px; width:50px;'>0</span>";
                    echo "<a style='text-decoration:none;' onclick='updatePfand(".$j.", true);' class='btn btn-default'><span class='glyphicon glyphicon-plus'></span></a></td></tr>";
                }
                $j++;
            }
        ?>
        </tbody></table>
    </div>
    <div class="panel panel-default">
        <div class="panel-heading">Neu</div>
        <div class="table-wrapper">
            <table class="table table-hover hyphenate">
                <thead><tr><th></th><th>Getränk</th><th>Kästen</th><th style='min-width:60px; max-width:75px;'>Fl.</th></tr></thead><tbody>
                <?php
                    foreach ($latest_products as $pr){
                        if(is_between($current_date, to_php_date($pr[1]), to_php_date($pr[2]))){
                            echo "<tr><td><img width='20px' src='img/drinks/".intval($pr[0]).".jpg' /></td><td class='hyphenate'>".$pr[3]."</td>";
                            echo "<td><div id='kastenNeu".intval($pr[0])."' class='btn-group nowrap-btn-group' data-toggle='buttons'>";
                            for($i = 0; $i < 6; $i++){
                                $active = "";
                                if($i == 0){
                                    $active = "active";
                                }
                                echo "<label class='small-btn btn btn-default ".$active."'><input name='kastenNeu".intval($pr[0])."' autocomplete='off' type='radio' value='".$i."' />".$i."</label>";
                            }
                            echo "</div></td>";
                            echo "<td style='min-width:60px; max-width:75px;'><input id='flaschenNeu".intval($pr[0])."' type='text' autocomplete='off' class='small-btn form-control' value='0' name='roomnumber' /></td></tr>";
                        }
                    }
                ?>
                </tbody>
            </table>
        </div>
    </div>
    <div id="finalizationPanel" class="panel panel-default">
        <div class="panel-heading">Abrechnen</div>
        <div class="panel-body">
            <div class="row"><div class="col-xs-6 col-md-2 vcenter">zu bezahlen:</div><div class="col-xs-5 col-md-9 vcenter"><span style="font-size:30px; font-weight:bold; color:green;"><span class="to_pay">0.00</span>€</span></div></div>
            <div class="row">
                <div class="col-xs-12">
                    <div id="kitchenSelection" class="btn-group" data-toggle="buttons">
                        <?php
                        $kitchens = explode(";", $config["kitchens"]);
                        foreach ($kitchens as $k){
                            echo "<label class='btn btn-default'>";
                            echo "<input name='kitchenSelection' autocomplete='off' type='radio' value='".$k."' />".$k."</label>";
                        }
                        ?>
                    </div>
                </div>
            </div>
            <div class="row" style="margin-top:10px;">
                <div class="col-xs-4 col-md-2"><button id="submitOrder" class="btn btn-default">Abschicken</button></div>
                <div class="col-xs-4 col-md-2"><button id="resetButton" class="btn btn-default">Reset</button></div>
            </div>
        </div>
    </div>
    <div class="panel panel-default">
        <div class="panel-heading">Preisliste</div>
        <div class="table-wrapper">
            <table class="table table-hover hyphenate">
                <thead><tr>
                    <th></th><th>Ge&shy;tränk</th><th>Kas&shy;ten&shy;preis (mit Pfand)</th><th>Fla&shy;schen&shy;preis (mit Pfand)</th><th>Kas&shy;ten&shy;pfand</th><th>Fla&shy;schen&shy;pfand</th><th>Trä&shy;ger&shy;pfand</th>
                </tr></thead><tbody>
                <?php
                    foreach ($latest_products as $pr){
                        if(is_between($current_date, to_php_date($pr[1]), to_php_date($pr[2]))){
                            echo "<tr><td><img width='20px' src='img/drinks/".intval($pr[0]).".jpg' /></td>";
                            echo "<td>".$pr[3]."</td>";
                            echo "<td>".number_format($pr[4], 2)."</td>";
                            echo "<td>".number_format($pr[5], 2)."</td>";
                            echo "<td>".number_format($pr[6], 2)."</td>";
                            echo "<td>".number_format($pr[7], 2)."</td>";
                            echo "<td>".number_format($pr[8], 2)."</td></tr>";
                        }
                    }
                ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<footer>
    <div class="vcenter">zu bezahlen:</div> <div class="vcenter"><span style="font-size:30px; font-weight:bold; color:green;"><span class="to_pay">0.00</span>€</span></div>
</footer>
<div id="pageOverlay">
    <img id="spinnerIcon" src="img/spinner.gif">
</div>
<script type="text/javascript">
var toPay = 0;
var selectedKitchen;
<?php
    $s_text = "";
    $p_text = "";
    foreach ($latest_products as $pr){
        $s_text .= "[0,0],";
        $p_text .= "[" . $pr[4] . "," . $pr[5] . "],";
    }
    $s_text = substr($s_text, 0, -1);
    $p_text = substr($p_text, 0, -1);
    echo "var selected = [" . $s_text . "];\n";
    echo "var prices = [" . $p_text . "];\n";

    $s_text = "";
    $p_text = "";
    foreach ($pfand as $p){
        $s_text .= "0,";
        $p_text .= $p.",";
    }
    $s_text = substr($s_text, 0, -1);
    $p_text = substr($p_text, 0, -1);
    echo "var selectedPfand = [" . $s_text . "];\n";
    echo "var pricesPfand = [" . $p_text . "];\n";
?>
$(document).ready(function(){
    <?php
    foreach ($latest_products as $pr){
        if(is_between($current_date, to_php_date($pr[1]), to_php_date($pr[2]))){
            $i = intval($pr[0]);
            echo "$('#kastenNeu".$i." :input').on('change', function(){";
            echo "selected[".$i."][0] = this.value | 0;";
            echo "updateToPay();";
            echo "});\n";

            echo "$('#flaschenNeu".$i."').on('input', function(){";
            echo "selected[".$i."][1] = this.value | 0;";
            echo "updateToPay();";
            echo "});\n";
        }
    }
    for($i = count($pfand_kasten)+count($pfand_traeger); $i < count($pfand); $i++){
        echo "$('#pfandDisplay".$i."').on('input', function(){";
        echo "selectedPfand[".$i."] = this.value | 0;";
        echo "updateToPay();";
        echo "});\n";
    }
    ?>

    $("#submitOrder").on('click', function(){
        selectedKitchen = $("#kitchenSelection input[type='radio']:checked").val();
        if(typeof selectedKitchen != 'undefined'){
            <?php
                $logged_in = false;
                if(isset($_COOKIE['pin'])){
                    if($_COOKIE['pin'] == hash("sha256", $config["getraenkemarkt_pw"].$config["cookie_secret"])){
                        $logged_in = true;
                    }
                }
                if($logged_in){
                ?>
                    $("#pageOverlay").css('display', 'block');
                    sendAjax();
                <?php
                }
                else{
                    echo '$("#pinModal").modal("show");';
                }
            ?>
        }
        else{
            alert("Bitte Küche des Käufers eingeben");
        }
    });
    $("#resetButton").on('click', reset);

    $('#pinModal').on('shown.bs.modal', function(){
        $('#pinInputField').focus();
    });
    $("#modalForm").submit(function(e){
        var pin = $("#pinInputField").val();
        if(pin != ""){
            $("#pinModal").modal('hide');
            $("#pageOverlay").css('display', 'block');
            sendAjax(pin);
            $("#pinInputField").val("");
        }
        e.preventDefault();
    });

    $("footer").on("click", function(){
        $('html, body').animate({
            scrollTop: $("#finalizationPanel").offset().top
        }, 500);
    });
});

function sendAjax(pin){
    var pfand = "";
    for(var i = 0; i < selectedPfand.length; i++){
        var current = selectedPfand[i];
        if(current != "0"){
            pfand += current + "x" + pricesPfand[i] + ";";
        }
    }
    var order = "";
    for(var i = 0; i < selected.length; i++){
        var current = selected[i][0] + "_" + selected[i][1];
        if(current != "0_0"){
            order += i + ":" + current + ";";
        }
    }
    var toSend = {
        selectedKitchen: selectedKitchen,
        pfand: pfand.slice(0, -1),
        order: order.slice(0, -1),
        totalSum: toPay.toFixed(2)
    };
    if(typeof pin !== "undefined"){
        toSend.pin = pin;
        toSend.remember = $("#rememberCheckbox").is(":checked") ? "1" : "0";
    }
    $.post("register_order.php", toSend, function(data){
        var jData = JSON.parse(data);
        if(jData.status == 0){
            alert(jData.msg);
            if(toSend.remember == "1"){
                location.reload();
            }
            else{
                reset();
            }
        }
        else{
            alert(jData.msg);
        }
        $("#pageOverlay").css('display', 'none');
    });
}

function updateToPay(){
    toPay = 0;
    for(var i = 0; i < selected.length; i++){
        toPay += selected[i][0] * prices[i][0];
        toPay += selected[i][1] * prices[i][1];
    }
    for(var i = 0; i < selectedPfand.length; i++){
        toPay -= selectedPfand[i] * pricesPfand[i];
    }
    $('.to_pay').html(toPay.toFixed(2));
    if(toPay <= 0){
        $('.to_pay').parent().css('color', 'green');
    }
    else{
        $('.to_pay').parent().css('color', 'red');
    }
}

function updatePfand(id, increase){
    if(increase){
        selectedPfand[id]++;
    }
    else{
        if(id >= <?php echo count($pfand_kasten)+count($pfand_traeger); ?> || selectedPfand[id] > 0){
            selectedPfand[id]--;
        }
    }
    if(id >= <?php echo count($pfand_kasten)+count($pfand_traeger); ?>){
        $("#pfandDisplay"+id).val(selectedPfand[id]);
    }
    else{
        $("#pfandDisplay"+id).html(selectedPfand[id]);
    }
    updateToPay();
}

function reset(){
    selectedKitchen = "";
    for(var i = 0; i < selectedPfand.length; i++){
        selectedPfand[i] = 0;
    }
    for(var i = 0; i < selectedPfand.length; i++){
        if(i >= <?php echo count($pfand_kasten)+count($pfand_traeger); ?>){
            $("#pfandDisplay"+i).val(0);
        }
        else{
            $("#pfandDisplay"+i).html(0);
        }
    }

    for(var i = 0; i < selected.length; i++){
        selected[i][0] = 0;
        selected[i][1] = 0;
    }
    for(var i = 0; i < selected.length; i++){
        $("#flaschenNeu"+i).val(0);
        $("#kastenNeu"+i+" > .active").removeClass("active");
        $("#kastenNeu"+i+" input[type='radio']:checked").prop('checked', false);

        $("#kastenNeu"+i+" :first-child").addClass("active");
        $("#kastenNeu"+i+" :first-child input[type='radio']").prop('checked', true);
    }

    $("#kitchenSelection > .active").removeClass("active");
    $("#kitchenSelection input[type='radio']:checked").prop('checked', false);

    updateToPay();
}
</script>
</body>
</html>