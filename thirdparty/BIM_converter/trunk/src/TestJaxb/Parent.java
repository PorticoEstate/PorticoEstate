package TestJaxb;

import java.util.ArrayList;
import java.util.List;

import javax.xml.bind.annotation.XmlElement;
import javax.xml.bind.annotation.XmlElementWrapper;
import javax.xml.bind.annotation.XmlRootElement;

@XmlRootElement
public class Parent {
   @XmlElementWrapper(name="children")
   @XmlElement(name="child") 
   List<Child> children = new ArrayList<Child>();
}

class Child {
	
}