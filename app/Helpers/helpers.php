<?php
  function convert_date($value) {
  return date('H:i:s - d M Y', strtotime($value));
  };

  function count_totalPriceProduct($price_product,$amount){
    $total = $price_product[0] * $amount;
    return $total;
  }

  function increase_stock($stockNow,$stockNew) {
    $stockNowArray = json_decode(json_encode($stockNow), true);
    $sto = strval($stockNew);
    $stockNewArray = array($sto);
    $merge = array_merge($stockNowArray,$stockNewArray);
    $sum = array_sum($merge);
    return $sum;  
  };

  function decrease_stock($stockNow,$stockNew) {
    $stockNowArray = json_decode(json_encode($stockNow), true);
    $sto = strval($stockNew);
    $stockNewArray = array(-$sto);
    $merge = array_merge($stockNowArray,$stockNewArray);
    $sum = array_sum($merge);
    return $sum;  
  };
?>