<?php
declare(strict_types=1);

namespace edwrodrig\cnv_reader;


class MetricInfoReader
{
    /**
     * @var string
     */
    private string $name;

    /**
     * @var string
     */
    private string $unit;

    /**
     * @var string
     */
    private string $type;

    /**
     * @var string[]
     */
    private array $other = [];

    /**
     * Unit are enclosed in square brackets
     */
    const UNIT_REGEX = '/\[([^\[\]]*)\]/';


    /**
     * MetricInfoParser constructor.
     *
     * @param string $line
     */
    public function __construct(string $line) {
        $tokens = explode(':', $line);
        $this->name = trim($tokens[0]);

        // if there are info more than the name
        if ( count($tokens) > 1 ) {
            $info = trim($tokens[1]);

            $unit = $this->retrieveUnit($info);
            if ( is_string($unit) )
                $this->unit = $unit;

            $info = preg_replace(self::UNIT_REGEX, '', $info);
            $info_sections = explode(',', $info);

            $info_sections = array_map(function ($element) {
                return trim($element);
            }, $info_sections);
            $info_sections = array_filter($info_sections, function ($element) {
                return !empty(trim($element));
            });
            if (count($info_sections) > 0) {
                $this->type = array_shift($info_sections);
                $this->other = $info_sections;
            }
        }



    }

    /**
     * Get the name of the metric column
     * @return string
     */
    public function getName() : string {
        return $this->name;
    }

    /**
     * Get the measurement unit.
     *
     * Example: mts
     * @return null|string
     */
    public function getUnit() : ?string {
        return $this->unit ?? null;
    }

    /**
     * Get the type of the
     * @return null|string
     */
    public function getType() : ?string {
        return $this->type ?? null;
    }

    /**
     * Get other data
     *
     * This in an array of other secondary data found in the header line, like vendor info, correlatives, etc
     * @return array
     */
    public function getOther() : array {
        return $this->other;
    }

    /**
     * Retrieve the unit from a string line.
     *
     * Generally the unit is some string enclosed by {@see MetricInfoParser::UNIT_REGEX square brackets}
     * @param string $info_section
     * @return null|string
     */
    private function retrieveUnit(string $info_section) : ?string {
        if ( preg_match(self::UNIT_REGEX, $info_section, $matches) ) {
            return trim($matches[1]);
        } else {
            return null;
        }

    }
}