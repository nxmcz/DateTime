<?php

declare(strict_types=1);

namespace Tests\Unit;

use DateTime;
use Noxem\DateTime\DT;
use Noxem\DateTime\Exception\BadFormatException;
use Tester\Assert;
use Tests\Fixtures\TestCase\AbstractTestCase;

require_once __DIR__ . '/../bootstrap.php';


/**
 * @testCase
 */
class DTTest extends AbstractTestCase
{
	public function setUp()
	{
		parent::setUp(); // TODO: Change the autogenerated stub
	}

	public function testCreate()
	{
		Assert::same(1654077600, DT::create(1654077600)->getTimestamp());
		Assert::same(1654077600, DT::create('1654077600')->getTimestamp());

		Assert::same(1654077600, DT::create('2022-06-01 12:00:00')->getTimestamp());
		Assert::same(time(), DT::create('now')->getTimestamp());
		Assert::same(time(), DT::create()->getTimestamp());

		Assert::type(DT::class, DT::create());
		Assert::exception(fn() => DT::create('foo foo Baz'), BadFormatException::class);

		Assert::true(
			DT::create("20.7.1993 00:30:00")
				->areEquals(DT::create(743121000))
		);

		Assert::true(
			DT::create()->setTime(20, 7, 0)
				->areEquals(DT::create('20.7.'))
		);

		Assert::true(
			DT::create()->setTime(13, 55, 27)
				->areEquals(DT::create('13:55:27'))
		);

		Assert::true(
			DT::create()->setTime(13, 55, 27, 689)
				->areEquals(DT::create('13:55:27.689'))
		);

		Assert::true(
			DT::create()->areEquals(DT::now())
		);

		Assert::true(
			DT::create()->areEquals(DT::parse())
		);

		Assert::true(
			DT::create("2022-07-20 11:22:33")->areEquals(DT::parse("2022-07-20 11:22:33"))
		);

		Assert::true(
			DT::create("2022-07-20 11:22:33")->areNotEquals(DT::parse("2022-07-20 11:22:34"))
		);

		Assert::true(
			DT::create("2022-07-20 11:22:33")->areNotEquals(DT::parse("2022-07-20 11:22:32"))
		);
	}

	public function testGetOrCreateInstance()
	{
		Assert::same(1654077600, DT::getOrCreateInstance(1654077600)->getTimestamp());

		$class = DT::create();
		Assert::type($class, DT::getOrCreateInstance($class));

		Assert::same(1654077600, DT::create('2022-06-01 12:00:00')->getTimestamp());
		Assert::same(time(), DT::getOrCreateInstance()->getTimestamp());

		Assert::type(DT::class, DT::getOrCreateInstance());
		Assert::exception(fn() => DT::getOrCreateInstance('foo foo Baz'), BadFormatException::class);

		Assert::true(DT::create()->areEquals(DT::getOrCreateInstance(new \DateTimeImmutable())));
		Assert::true(DT::create()->areEquals(DT::getOrCreateInstance(new DateTime())));
		Assert::true(DT::create()->areEquals(DT::getOrCreateInstance((new DateTime())->setTimezone(new \DateTimeZone('America/New_York')))));
	}

	public function testAreEquals()
	{
		Assert::true(
			DT::create('2022-07-20 01:00:00')
				->areEquals(DT::create('2022-07-20 01:00:00'))
		);

		Assert::true(
			DT::create('2022-07-20 01:00:00')
				->setTimezone(new \DateTimeZone('Europe/Prague'))
				->areEquals(DT::create('2022-07-20 01:00:00')->setTimezone(new \DateTimeZone('Europe/Prague')))
		);

		Assert::true(
			DT::create('2022-07-20 01:00:00')
				->setTimezone(new \DateTimeZone('Europe/Prague'))
				->areEquals(DT::create('2022-07-20 01:00:00')
					->setTimezone(new \DateTimeZone('America/New_York'))
				)
		);

		Assert::true(
			DT::create('2022-07-20 01:00:00')
				->areEquals(DT::create('2022-07-20 01:00:00')
					->setTimezone(new \DateTimeZone('America/New_York')))
		);

		Assert::true(
			DT::create('2022-07-20 01:00:00')
				->areEquals(DT::create('2022-07-19 19:00:00')
					->assignTimezone('America/New_York'))
		);
	}

	public function testIsFuture()
	{
		Assert::true(DT::create('now')->modify('+1 year')->isFuture());
		Assert::true(DT::create('now')->modify('+1 seconds')->isFuture());
		Assert::false(DT::create('now')->isFuture());
	}

	public function testDateParts()
	{
		$dt = DT::create('2022-07-20 19:00:00');

		Assert::same(2022, $dt->getYear());
		Assert::same(7, $dt->getMonth());
		Assert::same(20, $dt->getDay());
		Assert::same(19, $dt->getHour());
		Assert::same(29, $dt->getWeek());
	}

	public function testInitByConstructor()
	{
		$dt = new DT('2022-07-20 19:00:00');
		Assert::same('{"date":"2022-07-20 19:00:00.000000","timezone_type":3,"timezone":"Europe\/Prague"}', json_encode($dt));
	}

	public function testConverts()
	{
		$dt = DT::create('2022-07-20 19:00:00.677');
		Assert::same(677000, $dt->getMillisPart());

		$dt = DT::create('2022-07-20 19:00:00.0');
		Assert::same(0, $dt->getMillisPart());

		$dt = DT::create('2022-07-20 19:00:00');
		Assert::same(0, $dt->getMillisPart());

		$dt = DT::create('2022-07-20 19:00:00.123456789');
		Assert::same(123456, $dt->getMillisPart());

		$dt = DT::create('2022-07-20 19:00:00.1234569');
		Assert::same(123456, $dt->getMillisPart());
	}

	public function testToString()
	{
		Assert::same('2022-07-20 19:00:00', (string)DT::create('2022-07-20 19:00:00'));
	}

	/**
	 * @dataProvider providerOfDatesForDayOfWeek
	 */
	public function testGetDayOfWeek($date, $numberOfDay, $iso = TRUE): void
	{
		Assert::same($numberOfDay, DT::create($date)->getDayOfWeek($iso));
	}

	public function providerOfDatesForDayOfWeek(): array
	{
		return [
			['2022-08-29', 1],
			['2022-11-01', 2],
			['2022-09-14', 3],
			['2022-09-15', 4],
			['2022-12-23', 5],
			['2022-08-20', 6],
			['2023-01-01', 7],

			['2022-08-29', 2, FALSE],
			['2022-11-01', 3, FALSE],
			['2022-09-14', 4, FALSE],
			['2022-09-15', 5, FALSE],
			['2022-12-23', 6, FALSE],
			['2022-08-20', 7, FALSE],
			['2023-01-01', 1, FALSE]
		];
	}

	public function testGetDayOfYear(): void
	{
		Assert::same(1, DT::create('2022-01-01')->getDayOfYear());
		Assert::same(365, DT::create('2022-12-31')->getDayOfYear());
		Assert::same(366, DT::create('2020-12-31')->getDayOfYear());
	}

	public function testGetDaysOfYear(): void
	{
		Assert::same(365, DT::createFromParts(2022)->getDaysOfYear());
		Assert::same(366, DT::createFromParts(2020)->getDaysOfYear());
	}

	/**
	 * @dataProvider providerIsLeap
	 */
	public function testIsYearLeap(int $year, bool $isLeap): void
	{
		Assert::same($isLeap, DT::createFromParts(year: $year)->isYearLeap());
	}

	public function providerIsLeap(): array
	{
		return [
			[1595, FALSE],
			[1596, TRUE],
			[1597, FALSE],
			[1598, FALSE],
			[1599, FALSE],
			[1600, TRUE],
			[1601, FALSE],
			[1602, FALSE],
			[1603, FALSE],
			[1604, TRUE],
			[1605, FALSE],
			[1695, FALSE],
			[1696, TRUE],
			[1697, FALSE],
			[1698, FALSE],
			[1699, FALSE],
			[1700, FALSE],
			[1701, FALSE],
			[1702, FALSE],
			[1703, FALSE],
			[1704, TRUE],
			[1705, FALSE],
			[1795, FALSE],
			[1796, TRUE],
			[1797, FALSE],
			[1798, FALSE],
			[1799, FALSE],
			[1800, FALSE],
			[1801, FALSE],
			[1802, FALSE],
			[1803, FALSE],
			[1804, TRUE],
			[1805, FALSE],
			[1895, FALSE],
			[1896, TRUE],
			[1897, FALSE],
			[1898, FALSE],
			[1899, FALSE],
			[1900, FALSE],
			[1901, FALSE],
			[1902, FALSE],
			[1903, FALSE],
			[1904, TRUE],
			[1905, FALSE],
			[1995, FALSE],
			[1996, TRUE],
			[1997, FALSE],
			[1998, FALSE],
			[1999, FALSE],
			[2000, TRUE],
			[2001, FALSE],
			[2002, FALSE],
			[2003, FALSE],
			[2004, TRUE],
			[2005, FALSE],
			[2006, FALSE],
			[2007, FALSE],
			[2008, TRUE],
			[2009, FALSE],
			[2010, FALSE],
			[2011, FALSE],
			[2012, TRUE],
			[2013, FALSE],
			[2014, FALSE],
			[2015, FALSE],
			[2016, TRUE],
			[2017, FALSE],
			[2018, FALSE],
			[2019, FALSE],
			[2020, TRUE],
			[2021, FALSE],
			[2022, FALSE],
		];
	}

	/**
	 * @dataProvider providerOfYearsForMaximumWeek
	 */
	public function testWeeksOfYear(int $year, int $lastWeekNumber): void
	{
		Assert::same($lastWeekNumber, DT::createFromParts(year: $year)->getWeeksOfYear());
	}

	public function providerOfYearsForMaximumWeek(): array
	{
		return [
			[1900, 52],
			[1901, 52],
			[1902, 52],
			[1903, 53],
			[1904, 52],
			[1905, 52],
			[1906, 52],
			[1907, 52],
			[1908, 53],
			[1909, 52],
			[1910, 52],
			[1911, 52],
			[1912, 52],
			[1913, 52],
			[1914, 53],
			[1915, 52],
			[1916, 52],
			[1917, 52],
			[1918, 52],
			[1919, 52],
			[1920, 53],
			[1921, 52],
			[1922, 52],
			[1923, 52],
			[1924, 52],
			[1925, 53],
			[1926, 52],
			[1927, 52],
			[1928, 52],
			[1929, 52],
			[1930, 52],
			[1931, 53],
			[1932, 52],
			[1933, 52],
			[1934, 52],
			[1935, 52],
			[1936, 53],
			[1937, 52],
			[1938, 52],
			[1939, 52],
			[1940, 52],
			[1941, 52],
			[1942, 53],
			[1943, 52],
			[1944, 52],
			[1945, 52],
			[1946, 52],
			[1947, 52],
			[1948, 53],
			[1949, 52],
			[1950, 52],
			[1951, 52],
			[1952, 52],
			[1953, 53],
			[1954, 52],
			[1955, 52],
			[1956, 52],
			[1957, 52],
			[1958, 52],
			[1959, 53],
			[1960, 52],
			[1961, 52],
			[1962, 52],
			[1963, 52],
			[1964, 53],
			[1965, 52],
			[1966, 52],
			[1967, 52],
			[1968, 52],
			[1969, 52],
			[1970, 53],
			[1971, 52],
			[1972, 52],
			[1973, 52],
			[1974, 52],
			[1975, 52],
			[1976, 53],
			[1977, 52],
			[1978, 52],
			[1979, 52],
			[1980, 52],
			[1981, 53],
			[1982, 52],
			[1983, 52],
			[1984, 52],
			[1985, 52],
			[1986, 52],
			[1987, 53],
			[1988, 52],
			[1989, 52],
			[1990, 52],
			[1991, 52],
			[1992, 53],
			[1993, 52],
			[1994, 52],
			[1995, 52],
			[1996, 52],
			[1997, 52],
			[1998, 53],
			[1999, 52],
			[2000, 52],
			[2001, 52],
			[2002, 52],
			[2003, 52],
			[2004, 53],
			[2005, 52],
			[2006, 52],
			[2007, 52],
			[2008, 52],
			[2009, 53],
			[2010, 52],
			[2011, 52],
			[2012, 52],
			[2013, 52],
			[2014, 52],
			[2015, 53],
			[2016, 52],
			[2017, 52],
			[2018, 52],
			[2019, 52],
			[2020, 53],
			[2021, 52],
			[2022, 52],
			[2023, 52],
			[2024, 52],
			[2025, 52],
			[2026, 53],
			[2027, 52],
			[2028, 52],
			[2029, 52],
			[2030, 52],
			[2031, 52],
			[2032, 53],
			[2033, 52],
			[2034, 52],
			[2035, 52],
			[2036, 52],
			[2037, 53],
			[2038, 52],
			[2039, 52],
			[2040, 52],
			[2041, 52],
			[2042, 52],
			[2043, 53],
			[2044, 52],
			[2045, 52],
			[2046, 52],
			[2047, 52],
			[2048, 53],
			[2049, 52],
			[2050, 52],
			[2051, 52],
			[2052, 52],
			[2053, 52],
			[2054, 53],
			[2055, 52],
			[2056, 52],
			[2057, 52],
			[2058, 52],
			[2059, 52],
			[2060, 53],
			[2061, 52],
			[2062, 52],
			[2063, 52],
			[2064, 52],
			[2065, 53],
			[2066, 52],
			[2067, 52],
			[2068, 52],
			[2069, 52],
			[2070, 52],
			[2071, 53],
			[2072, 52],
			[2073, 52],
			[2074, 52],
			[2075, 52],
			[2076, 53],
			[2077, 52],
			[2078, 52],
			[2079, 52],
			[2080, 52],
			[2081, 52],
			[2082, 53],
			[2083, 52],
			[2084, 52],
			[2085, 52],
			[2086, 52],
			[2087, 52],
			[2088, 53],
			[2089, 52],
			[2090, 52],
			[2091, 52],
			[2092, 52],
			[2093, 53],
			[2094, 52],
			[2095, 52],
			[2096, 52],
			[2097, 52],
			[2098, 52],
			[2099, 53],
			[2100, 52]
		];
	}

	public function testCreateBadParts(): void
	{
		Assert::exception(fn() => DT::createFromParts(2022, second: 60), BadFormatException::class);
	}

	public function testToJson(): void
	{
		Assert::same(
			json_encode(DT::create('2022-05-20 11:45:00')),
			'{"date":"2022-05-20 11:45:00.000000","timezone_type":3,"timezone":"Europe\/Prague"}'
		);

		Assert::same(
			json_encode(DT::create('2022-05-20 11:45:00.1234')),
			'{"date":"2022-05-20 11:45:00.123400","timezone_type":3,"timezone":"Europe\/Prague"}'
		);

		Assert::same(
			json_encode(DT::create('2022-05-20 11:45:00.1234')->setTimezone(new \DateTimeZone("Asia/Tokyo"))),
			'{"date":"2022-05-20 18:45:00.123400","timezone_type":3,"timezone":"Asia\/Tokyo"}'
		);
	}

	public function test_set_timezone(): void
	{
		Assert::true(
			DT::create('2022-05-20 11:45:00.1234')->setTimezone("Asia/Tokyo")
				->areEquals(
					DT::create('2022-05-20 11:45:00.1234')->setTimezone(new \DateTimeZone("Asia/Tokyo"))
				)
		);

		Assert::false(
			DT::create('2022-05-20 11:45:00.1234')->setTimezone("America/New_York")
				->areEquals(
					DT::create('2022-05-20 11:45:00.1234')->assignTimezone("Asia/Tokyo")
				)
		);
	}
}

$test = new DTTest();
$test->run();
