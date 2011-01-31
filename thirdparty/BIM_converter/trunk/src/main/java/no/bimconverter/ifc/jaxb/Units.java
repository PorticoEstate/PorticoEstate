package no.bimconverter.ifc.jaxb;

import java.util.List;
import javax.xml.bind.JAXBElement;
import javax.xml.bind.annotation.XmlAnyElement;
import javax.xml.bind.annotation.XmlRootElement;

@XmlRootElement
public class Units extends ElementList{
	//private List<JAXBElement<String>> units = new ArrayList<JAXBElement<String>>();
	
	public Units() {
	}
	
	
	@XmlAnyElement
	public List<JAXBElement<String>> getUnits() {
	    return elementList;
	}
	
	
	public void setUnits(List<JAXBElement<String>> units) {
		this.elementList = units;
	}

}
