<?php
	/**
	 * A PHP implementation of Green's Theorem to determine the area of an irregular polygon
	 *
	 * @author Samuel Adeshina <samueladeshina73@gmail.com> <http://samshal.githb.io>
	 * @copyright 2016 samuel adeshina, all rights reserved
	 * @license MIT
	 * @since 1.0 October, 2015
	 * @version 1.0.1
	 */

	/**
	 * We include the "point.class.php" class file below using the require keyword.
	 * This line should be removed if the parent project has an autoloader.
	*/
	require_once("point.class.php"); //imports the Point object

	class Polygon
	{
		protected $vertices = array();
		private $pointObj;

		/**
		*	constructor
		*
		*	accepts an unlimited number of  Point objects (vertices) to represent the coordinates of 
		*	the polygon on an x-y plane
		*
		*/
		public function __construct($vertice = null)
		{
			$this->pointObj = new Point();
			try
			{
				foreach (func_get_args() as $vertice) //Constructor accepts unlimited number of Points (Point Objects)
				{
					if (!is_null($vertice))
					{
						self::setVertice($vertice);
					}
				}
			}
			catch(Exception $e)
			{
				return self::displayPointInvalidError();
			}
		}

		/**
		 * displayPointInvalidError()
		 * throws an exception, accepts an optional parameter that determines if
		 * the already predefined vertice-array should be emptied or not
		 *
		 * @param boolean $empty
		*/
		private function displayPointInvalidError($empty = false)
		{
			echo "Invalid point detected in supplied parameters";
			if ($empty)
			{
				$this->vertices = array();
			}
		}
		
		/**
		 * setVertice()
		 * accepts a single point object and appends it's value to the already
		 * predfined $vertices array
		 * parameter must a Point Object (example: new Point(2, 3));
		 *
		 * @param \point $vertice
		*/

		public function setVertice($vertice) //Accepts a single point object (a single coordinate)
		{
			try
			{
				if (!is_array($vertice))
				{
					$this->vertices[] = $vertice;
				}
				else if (is_object($vertice) && ($vertice instanceof $this->pointObj))
				{
					$this->vertices[] = $vertice->point();
				}
				else
				{
					throw new \Exception();
				}
			}
			catch(Exception $e)
			{
				return self::displayPointInvalidError();
			}
		}

		/*
			@method: setVertices()
			extends the setVertice method, unlike the setVertice method
			it accepts multiple parameters
			example implementation (setVertices(new Point(2, 34), new Point(3, 12), ))
		*/

		public function setVertices() //Accepts more that one point objects (Multiple coordinates)
		{
			try
			{
				foreach (func_get_args() as $vertice)
				{
					if (!is_null($vertice))
					{
						self::setVertice($vertice);
					}
				}
			}
			catch(Exception $e)
			{
				return self::displayPointInvalidError();
			}
		}

		private function isPolygon()
		{
			//need to check if we've got a valid irregular polygonal shape from the supplied vertices
			return true;
		}

		public function area() //Calculates area of polygon
		{
			if (self::isPolygon())
			{
				$points = count($this->vertices);
				$verticeSigma = 0;
				for ($i = 0; $i < $points - 1; $i++)
				{
					if (isset($this->vertices[$i]) && isset($this->vertices[$i+1]))
					{
						$xCurrent = $this->vertices[$i]->point[0];
						$yCurrent = $this->vertices[$i]->point[1];
						$xNext = $this->vertices[$i+1]->point[0];
						$yNext = $this->vertices[$i+1]->point[1];
						$verticeSigma += (($xNext + $xCurrent)*($yNext - $yCurrent)) / 2;
					}
				}
				$xLast = (($this->vertices[0]->point[0] + $this->vertices[$points-1]->point[0]));
				$yLast = (($this->vertices[0]->point[1] - $this->vertices[$points-1]->point[1]));
				$verticeSigma += ($xLast * $yLast) / 2;
				return $verticeSigma;
			}
		}

		/*
			@method = perimeter()
			Determines the distance round a polygonal object or shape
			checks if a line is straight or diagonal before applying an 
			aroriate an formular
		*/

		public function perimeter() //calculates distance around the polygon
		{
			if (self::isPolygon())
			{
				$points = count($this->vertices);
				$verticeSigma = 0;
				for ($i = 0; $i < $points - 1; $i++)
				{
					if (isset($this->vertices[$i]) && isset($this->vertices[$i+1]))
					{
						$edgeOneCoords = $this->vertices[$i];
						$edgeTwoCoords = $this->vertices[$i+1];
						$xedgeOne = $edgeOneCoords->point[0];
						$xedgeTwo = $edgeTwoCoords->point[0];
						$yedgeOne = $edgeOneCoords->point[1];
						$yedgeTwo = $edgeTwoCoords->point[1];
						$lineType = self::checkLineType(array($xedgeOne, $xedgeTwo), array($yedgeOne, $yedgeTwo));
						if ($lineType == 1)
						{
							if ($xedgeOne == $xedgeTwo)
							{
								$distance = abs($yedgeOne - $yedgeTwo);
							}
							else
							{
								$distance = abs($xedgeOne - $xedgeTwo);
							}
						}
						else if ($lineType == 0)
						{
							$distance = sqrt(pow(($xedgeTwo - $xedgeOne), 2) + pow(($yedgeTwo - $yedgeOne), 2));
						}
						else
						{
							throw new \Exception("A coordinate resulted in an invalid line type, please review the supplied parameters");
						}

						print_r(array(array($xedgeOne, $xedgeTwo), array($yedgeOne, $yedgeTwo)));
						$verticeSigma += $distance;
					}
				}
				$firstSide = $this->vertices[0];
				$lastSide = $this->vertices[$points - 1];
				$lineType = self::checkLineType(
									array($firstSide->point[0], $lastSide->point[0]),
									array($firstSide->point[1], $lastSide->point[1])
								);
				if ($lineType == 1)
				{
					if ($xedgeOne == $xedgeTwo)
					{
						$distance = abs($yedgeOne - $yedgeTwo);
					}
					else
					{
						$distance = abs($xedgeOne - $xedgeTwo);
					}
				}
				else if ($lineType == 0)
				{
					$distance = sqrt(pow(($xedgeTwo - $xedgeOne), 2) + pow(($yedgeTwo - $yedgeOne), 2));
				}
				else
				{
					throw new \Exception("A coordinate resulted in an invalid line type, please review the supplied parameters");
				}
				$verticeSigma += $distance;

				return $verticeSigma;
			}
		}

		/*
			@method = checkLineType
			routes between the isStraightLine() and isDiagonalLine() methods for determining the line type between
			row vertices (coordinates)

		*/
		private function checkLineType($x, $y) //determines if a line is straight or diagonal
		{
			if (!is_array($x) || !is_array($y))
			{
				throw new \Exception("Array values were expected as parameters to the checkLineType method");
			}
			else
			{
				if (self::isStraightLine($x, $y))
				{
					return 1;
				}
				else if (self::isDiagonalLine($x, $y))
				{
					return 0;
				}
				else
				{
					return -1;
				}

			}
		}

		/*
			Determines if a line is a Straight line
		
		*/
		public function isStraightLine($x, $y)
		{
			if ($x[0] == $x[1] || $y[0] == $y[1])
			{
				return true;
			}
			else
			{
				return false;
			}
		}

		/*
			@method: isDiagonalLine()
			Determines if a line is diagonal
		*/
		public function isDiagonalLine($x, $y)
		{
			if (!$this->isStraightLine($x, $y))
			{
				return true;
			}
			else
			{
				return false;
			}
		}

		/*
			@method: getVertices()
			The user may need to view an array of the predefined vertices, hence the function below
		*/
		public function getVertices() //returns the supplied vertices
		{
			return $this->vertices;
		}
	}
?>

