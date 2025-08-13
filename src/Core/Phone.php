<?php
declare(strict_types=1);

namespace Laque\Identity\Core;

final class Phone
{
	/** Normalize to +255XXXXXXXXX (TZ) */
	public static function normalize(string $phone): string
	{
		$digits = preg_replace('/\D+/', '', $phone) ?? '';
		if ($digits === '') {
			return $phone; // nothing to do
		}

		// Derive national 9-digit part
		if (str_starts_with($digits, '255')) {
			$national = substr($digits, 3);
		} elseif (str_starts_with($digits, '0')) {
			$national = substr($digits, 1);
		} elseif (strlen($digits) === 9) {
			$national = $digits;
		} else {
			// fall back: keep last 9 digits
			$national = substr($digits, -9);
		}

		// Ensure exactly 9 digits
		$national = substr(preg_replace('/\D+/', '', $national) ?? '', -9);

		return '+255' . $national;
	}

	/** Very basic operator detection using prefix tables (override via data/operator_prefixes.php). */
	public static function operator(string $phone): string
	{
		$n = ltrim(self::normalize($phone), '+');
		if (!str_starts_with($n, '255') || strlen($n) < 6) {
			return 'unknown';
		}

		$national = substr($n, 3); // e.g., 712345678
		$three    = substr($national, 0, 3);
		$two      = substr($national, 0, 2);
		$three0   = '0' . substr($national, 0, 2);

		// Defaults (can be overridden by data/operator_prefixes.php)
		/** @var array{3digit: array<string,string>, 2digit: array<string,string>} $map */
		$map = [
			'3digit' => [
				'074' => 'vodacom', '075' => 'vodacom',
				'068' => 'airtel',  '078' => 'airtel',
				'065' => 'tigo',    '071' => 'tigo',
				'062' => 'halotel', '073' => 'ttcl', '077' => 'zantel',
			],
			'2digit' => [
				'74' => 'vodacom', '75' => 'vodacom',
				'68' => 'airtel',  '78' => 'airtel',
				'65' => 'tigo',    '71' => 'tigo',
				'62' => 'halotel', '73' => 'ttcl', '77' => 'zantel',
			],
		];

		$mapFile = __DIR__ . '/../../data/operator_prefixes.php';
		if (is_file($mapFile)) {
			$loaded = include $mapFile;
			if (is_array($loaded)) {
				// favor external definitions (Open/Closed: extend without modifying class)
				$map['3digit'] = array_merge($map['3digit'], $loaded['3digit'] ?? []);
				$map['2digit'] = array_merge($map['2digit'], $loaded['2digit'] ?? []);
			}
		}

		if (isset($map['3digit'][$three])) {
			return $map['3digit'][$three];
		}
		if (isset($map['3digit'][$three0])) {     // <-- NEW (supports your '071','074',...)
			return $map['3digit'][$three0];
		}
		if (isset($map['2digit'][$two])) {
			return $map['2digit'][$two];
		}

		return 'unknown';
	}
}
