package no.bimconverter.ifc.jaxb;

import java.util.List;

import javax.xml.bind.JAXBElement;
import javax.xml.bind.annotation.XmlAnyElement;
import javax.xml.bind.annotation.XmlAttribute;
import javax.xml.bind.annotation.XmlRootElement;
@XmlRootElement
public class PropertyList extends ElementList{
	
	private String name;
	private String type;
	
	//private List<NameValuePair> properties = new ArrayList<NameValuePair>();
	
	public PropertyList() {
	}
	/*
	public void setProperties(List<NameValuePair> baseQuantities) {
		this.properties = baseQuantities;
	}
	
	
	@XmlElement(name="property") 
	public List<NameValuePair> getProperties() {
		return properties;
	}
	*/
	@XmlAnyElement
	public List<JAXBElement<String>> getProperties() {
	    return elementList;
	}
	
	
	public void setProperties(List<JAXBElement<String>> units) {
		this.elementList = units;
	}

	public void setName(String propertySetName) {
		this.name = propertySetName;
	}
	@XmlAttribute
	public String getName() {
		return name;
	}

	public void setType(String type) {
		this.type = type;
	}
	@XmlAttribute
	public String getType() {
		return type;
	}
	

}
