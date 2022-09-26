<?php

declare(strict_types=1);

namespace Tests\Unit;

use DateTime;
use Noxem\DateTime\DT;
use Noxem\DateTime\Exception\BadFormatException;
use Noxem\DateTime\Exception\InvalidArgumentException;
use Tester\Assert;
use Tests\Fixtures\TestCase\AbstractTestCase;

require_once __DIR__ . '/../bootstrap.php';


/**
 * @testCase
 */
class AdditionTest extends AbstractTestCase
{
	public function testSituations()
	{
		$res = [];
		$dt = DT::create("2022-05-05 15:00:00");

		$res[] = $dt;
		$res[] = $dt->addSeconds(20);
		$res[] = $dt->subSeconds(150);
		$res[] = $dt->addMinutes(4);
		$res[] = $dt->subMinutes(10);
		$res[] = $dt->addHours(6);
		$res[] = $dt->subHours(25);
		$res[] = $dt->addDays(3);
		$res[] = $dt->subDays(1);
		$res[] = $dt->addMonth(2);
		$res[] = $dt->subMonth(8);

		$mirror = [
			DT::create("2022-05-05 15:00:00"),
			DT::create("2022-05-05 15:00:20"), // 1st
			DT::create("2022-05-05 14:57:30"),
			DT::create("2022-05-05 15:04:00"),
			DT::create("2022-05-05 14:50:00"),
			DT::create("2022-05-05 21:00:00"),
			DT::create("2022-05-04 14:00:00"),
			DT::create("2022-05-08 15:00:00"),
			DT::create("2022-05-04 15:00:00"),
			DT::create("2022-07-05 15:00:00"),
			DT::create("2021-09-05 15:00:00"),
		];

		Assert::same(
			array_map(fn($v) => $v->getTimestamp(), $res),
			array_map(fn($v) => $v->getTimestamp(), $mirror)
		);

		Assert::true($dt->areEquals(DT::create("2022-05-05 15:00:00")));
	}

	public function testInvalidArgumentException(): void
	{
		$dt = DT::create("2022-05-05 15:00:00");

		Assert::exception(fn() => $dt->addSeconds(-1), InvalidArgumentException::class, "seconds must be only positive numbers");
		Assert::exception(fn() => $dt->subHours(-1), InvalidArgumentException::class, "hours must be only positive numbers");
	}

	public function testChasing(): void
	{
		$dt = DT::create("2022-05-05 15:00:00");

		Assert::true(
			DT::create("2022-04-05 14:30:00")
				->areEquals(
					$dt
						->addSeconds(180)
						->subSeconds(480)
						->addMonth(2)
						->subMonth(6)
						->subHours(9)
						->subDays(1)
						->addDays(91)
						->addMinutes(515)
				)
		);
	}
}
$test = new AdditionTest();
$test->run();