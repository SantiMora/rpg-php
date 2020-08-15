<?php

namespace RPG;

class Dice {

	public const DICE_ROLL_REGEX = "(?:(?:^|\+)(?:(?:(?<diceNumber>\d*)?d(?<diceSize>\d+))|(?<rawNumber>\d+)))+?";

	static public function validateRoll(string $diceRegex): void
	{
		if (! preg_match(sprintf("/^%s$/", self::DICE_ROLL_REGEX), $diceRegex)) {
			throw new \Exception("Not valid dice roll", 1);
		}
	}

	static function roll1d($diceSize): int
	{
		return rand(1, $diceSize);
	}

	static public function roll(string $diceRegex)
	{
		self::validateRoll($diceRegex);

		if (preg_match_all(sprintf("/%s/", self::DICE_ROLL_REGEX), $diceRegex, $matches, PREG_SET_ORDER) !== false) {

			$total = 0;
			$addends = [];

			foreach ($matches as $addend) {
				
				if (isset($addend["rawNumber"]) && ! empty($addend["rawNumber"])) {

					$total += $addend["rawNumber"];
					array_push($addends, $addend["rawNumber"]);

				} else {

					$addend["diceNumber"] = (int) ! empty($addend["diceNumber"]) ? $addend["diceNumber"] : 1;

					$results = array_map(function () use ($addend) {
						return rand(1, $addend["diceSize"]);
					}, array_fill(0, $addend["diceNumber"], null));

					$total += array_sum($results);
					array_push($addends, $results);
				}
			}

			return (object) [
				"result" => $total,
				"result_explained" => $addends
			];

		} else {
			throw new \Exception("Error Processing Regex", 1);
		}
	}
}