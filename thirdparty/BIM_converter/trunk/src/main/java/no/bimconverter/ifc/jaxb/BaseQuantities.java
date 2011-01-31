package no.bimconverter.ifc.jaxb;

import java.util.List;

import javax.xml.bind.JAXBElement;
import javax.xml.bind.annotation.XmlAnyElement;
import javax.xml.bind.annotation.XmlRootElement;

@XmlRootElement
public class BaseQuantities extends ElementList {
	public BaseQuantities() {
	}
	
	@XmlAnyElement
	public List<JAXBElement<String>> getBaseQuantities() {
	    return elementList;
	}
	
	
	public void setBaseQuantities(List<JAXBElement<String>> units) {
		this.elementList = units;
	}

}
