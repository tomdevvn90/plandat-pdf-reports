<?php
	class Point
	{
		public $point;
		public function __construct($x = 0, $y = 0)
		{
			if (!is_numeric($x) || !is_numeric($y))
			{
				throw new \Exception("Invalid Point Specified");
			}
			else
			{
				$this->point = array($x, $y);
			}
		}

		protected function isPoint()
		{
			//checks to determine we've got a valid point
			
		}

		private function toString()
		{
			return $this->point[0].", ".$this->point[1];
		}

		public function point()
		{
			return toString();
		}
	}
?>