<?php
declare(strict_types=1);

/**
 * Функция выводит на экран список рабочих и выходных дней сотрудника
 *
 * @param int|null $month - месяц, число 1-12
 * @param int|null $year - год, 4х-значное число
 * @param int $cntMonth - кол-во месяцев, число
 * @return void
 * @throws Exception
 */
function viewWorkDays(int $month = null, int $year = null, int $cntMonth = 0): void
{
  if ($month === null) {
    $month = (int) date('m');
  }

  if ($month < 1 || $month > 12) {
    throw new Exception("Month must be between 1 and 12");
  }

  if ($year === null) {
    $year = (int) date('Y');
  }

  if ($year < 1970) {
    throw new Exception("Year must be 1970");
  }

  if ($cntMonth === 0) {
    $cntDays = date('t', mktime(0, 0, 0, $month, 1, $year));
  } else {
    $timestamp = mktime(0, 0, 0, $month, 1, $year);
    $dateFrom = new DateTime();
    $dateFrom->setTimestamp($timestamp);
    $dateTo = new DateTime();
    $dateTo->setTimestamp($timestamp)->modify("+ $cntMonth month");

    $cntDays = $dateFrom->diff($dateTo)->days;
  }

  $cnt = 0;
  for ($i = 1; $i <= $cntDays; $i++) {
    $cnt++;
    $timestamp = mktime(0, 0, 0, $month, $i, $year);
    $date = getFormatDate($timestamp);

    if (date('N', $timestamp) === '6') {
      $cnt = 2;
    } elseif (date('N', $timestamp) === '7') {
      $cnt = 3;
    }

    if ($cnt === 1) {
      echo getWorkDay($date, 'working day');
    } else {
      echo getDayOff($date, 'day-off');
    }

    $cnt = $cnt === 3 ? 0 : $cnt;
  }
}

/**
 * Функция возвращает дату в формате: день месяца месяц год, день недели
 *
 * @param int $timestamp - метка времени
 * @return string
 */
function getFormatDate(int $timestamp): string
{
  $date = date('j F Y, l', $timestamp);

  if (in_array(date('N', $timestamp), ['6', '7'], true)) {
    $date = "\033[31m$date\033[0m";
  }

  return $date;
}

/**
 * Функция возвращает строку с рабочим днем в желтом цвете
 *
 * @param string $date
 * @param string $text
 * @return string
 */
function getWorkDay(string $date, string $text): string
{
  return $date . " - \033[33m$text\033[0m" . PHP_EOL;
}

/**
 * Функция возвращает строку с выходным днем в зеленом цвете
 *
 * @param string $date
 * @param string $text
 * @return string
 */
function getDayOff(string $date, string $text): string
{
  return $date . " - \033[32m$text\033[0m" . PHP_EOL;
}

try {
  viewWorkDays();
} catch (Exception $exception) {
  echo $exception->getMessage();
}

