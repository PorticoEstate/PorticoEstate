package no.bimfm.jaxb;

import java.util.ArrayList;
import java.util.HashMap;
import java.util.List;
import java.util.Map;

import javax.xml.bind.JAXBElement;
import javax.xml.bind.annotation.XmlTransient;
import javax.xml.namespace.QName;

public class ElementList {
	protected List<JAXBElement<String>> elementList = new ArrayList<JAXBElement<String>>();
	
	public ElementList() {
	}
	
	public void addElement(String name, String value) {
		elementList.add(new JAXBElement<String>(new QName(name), String.class, value));
	}
	/*
	 * @return true if element was updated
	 */
	public boolean changeElementValue(String name, String value) {
		for(int i = 0; i < elementList.size(); i++) {
			String elementName = elementList.get(i).getName().toString();
			if( elementName.equals(name)) {
				elementList.set(i, new JAXBElement<String>(new QName(name), String.class, value));
				return true;
			}
		}
		return false;
	}
	
	@XmlTransient
	public Map<String, String> getElementMap() {
		if(this.elementList.size() == 0) {
			return null;
		}
		Map<String, String> outputMap = new HashMap<String, String>();
		for(JAXBElement<String> jaxbUnit : this.elementList) {
			outputMap.put(jaxbUnit.getName().toString(), jaxbUnit.getValue());
		}
		return outputMap;
	}

	
}
