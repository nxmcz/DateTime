<?php

declare(strict_types=1);

namespace Tests\Unit;

use Noxem\DateTime\DateTimeImmutable as DateTime;
use Noxem\DateTime\Difference;
use Noxem\DateTime\Exception\BadFormatException;
use Noxem\DateTime\Term;
use Tester\Assert;
use Tests\Fixtures\TestCase\AbstractTestCase;

require_once __DIR__ . '/../bootstrap.php';


/**
 * @testCase
 */
class TermTest extends AbstractTestCase
{
	public function setUp()
	{
		parent::setUp(); // TODO: Change the autogenerated stub
		date_default_timezone_set('Europe/Prague');
	}

	public function testValid()
	{
		$difference = new Difference(
			$start = DateTime::create('2019-08-01 20:55:59'),
			$end = DateTime::create('2019-08-02 20:55:59')
		);

		Assert::same($start, $difference->getStartDT());

		Assert::same(1564685759000, $difference->getStartMilis());
		Assert::same(1564772159000, $difference->getEndMilis());

		Assert::same($end, $difference->getEndDT());
		Assert::type(\DateInterval::class, $difference->getIntervalDT());
		Assert::same($end, $difference->getEndDT());

		Assert::same(86400000, $difference->msec());
		Assert::same(86400, $difference->seconds());
		Assert::same(1440.0, $difference->minutes());
		Assert::same(24.0, $difference->hours());

		Assert::false($difference->isActive());

		$live = new Difference(
			DateTime::create('now')->modify("-9 minutes"),
			DateTime::create()->modify("+10 minutes")
		);

		Assert::true($live->isActive());
		Assert::same(540, $live->intervalToNow());
		Assert::same(600, $live->intervalToEnd());
	}

	public function testInvalid()
	{
		Assert::exception(
			fn() => new Difference(
				DateTime::create('2019-08-01 20:55:59'),
				DateTime::create('2019-08-01 20:55:59')->modify("-1 seconds")
			),
			\InvalidArgumentException::class
		);
	}
}

$test = new TermTest();
$test->run();