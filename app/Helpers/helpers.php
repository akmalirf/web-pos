<?php
  function convert_date($value) {
  return date('H:i:s - d M Y', strtotime($value));
  };

  function count_totalPriceProduct($price_product,$amount){
    $total = $price_product[0] * $amount;
    return $total;
  }

  function update_stock($amountOld,$amountNew){
    $total = $amountOld - $amountNew;
    return $total;
  }

  function addSameProduct($amountOld,$amountNew){
    $total = $amountOld + $amountNew;
    return $total;
  }

  function countProfit($Pricebuy,$Pricesell){
    $profit = $Pricesell - $Pricebuy;
    return $profit;
  }

  


