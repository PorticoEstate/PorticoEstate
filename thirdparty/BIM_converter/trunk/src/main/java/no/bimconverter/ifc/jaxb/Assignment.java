package no.bimconverter.ifc.jaxb;

import java.util.ArrayList;
import java.util.List;

import javax.xml.bind.annotation.XmlRootElement;

@XmlRootElement
public class Assignment {
	public Assignment() {
	}
	protected List<String> checkAndInitializeArrayList(List<String> list) {
		if(list == null) {
			return new ArrayList<String>();
		} else {
			return list;
		}
	}

}
