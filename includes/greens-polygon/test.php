<?php
	require_once("polygon.class.php");

	$polygon = new Polygon();
	$polygon->setVertices(new Point(0, 0), new Point(1.5, 0), new Point(2.5, -1), new Point(2, 5));

	echo ($polygon->area()); //returns the area of the irregular polygonal shape
	echo ($polygon->perimeter()); //returns the perimeter of the polygon (the distance around the polygonal shape or object)

?>
