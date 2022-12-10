<?php

declare(strict_types=1);

namespace Noxem\DateTime\LocalDate;

use Noxem\DateTime\Difference;
use Noxem\DateTime\DT;
use Noxem\DateTime\Utils\Formatter;

class Week extends DatePart
{
	public function getMaximumNumber(): int
	{
		return $this->dt->getWeeksOfYear();
	}

	public function getNumber(): int
	{
		return $this->dt->getWeek();
	}

	public function getFormat(): string
	{
		return Formatter::WEEK_NUM;
	}

	public function difference(): Difference
	{
		return new Difference($this->getDT(), $this->getDT()->addDays(7));
	}
}
