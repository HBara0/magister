<?php

require '../inc/init.php';
$budgetrates = BudgetFxRates::get_data();

foreach ($budgetrates as $rate) {
    $budgetrates_reverse = BudgetFxRates::get_data(array('year' => $rate->year, 'affid' => $rate->affid, 'toCurrency' => $rate->fromCurrency, 'fromCurrency' => $rate->toCurrency));
    if (!is_object($budgetrates_reverse)) {
        $reversearray = array('fromCurrency' => $rate->toCurrency,
            'toCurrency' => $rate->fromCurrency,
            'affid' => $rate->affid,
            'year' => $rate->year,
            'rate' => 1 / $rate->rate,
        );
        $db->insert_query('budgeting_fxrates', $reversearray);
        //   $reversed_data = array_combine(array_keys($data), array_reverse(array_values($data)));
    }
}

