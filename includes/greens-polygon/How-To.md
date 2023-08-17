###How To Instantiate This Class  

It determines the area and perimeter of a polygon by accepting the vertices (_coordinates_) of the polygon.  
_These parameters can be provided in three different ways_:  
1. **Supplying them in the constructor definition** 
  You can do this by adding the coordinates as a Point object as parameter.  
  
  A **Point Object** is a new instance of a point class that's already been included in the
  `polygon.class.php` class file. You can find the source code of this class in the `point.class.php` class file  
  To provide the vertices of the polygon as parameters of the constructor, _you may instantiate it this way_: 
  
    `$polygonObjectInstance = new Polygon(new Point(0, 0), new Point(2), ...);`  
  The constructor accepts as many parameters as is possible, _although, they're all optional_  
2. **Providing them as parameters to the setVertice() method**  
  The setVertice method accepts a single parameter which must be an instance of the Point object.
  This parameter acts as the coordinate/vertex of a polygon  
    
    `....`  
    `$pointObjectInstance->setVertice(new Point());`   
    `$pointObjectInstance->setVertice(new Point(2, 3));`  
    `//This option provides a coordinate with the x and y axis at the (0, 0) and (2, 3) positions on a plane` 
3. **Providing them as parameters to the setVertices() method**
  Unlike the setVertice method, this method accepts more than one parameter just like the constructor but they all
  do the same thing, to populate an array with a set of coordinates.
  
  **You can get both the area and parameter by calling either of the `area()` or `perimeter()` methods respectively**
  
[Wanna talk about this class?](http://samshal.github.io)
  
  

