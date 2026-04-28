<?php
require_once 'DAL/AnalyticsDAL.php';

$analytics = new AnalyticsDAL();
$data = $analytics->getIncomeExpense();

echo "<pre>";
print_r($data);
?>