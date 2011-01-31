package no.bimconverter.ifc.jaxb.rest;

import java.util.ArrayList;
import java.util.List;

import javax.xml.bind.annotation.XmlElement;
import javax.xml.bind.annotation.XmlRootElement;



@XmlRootElement
public class SimpleList {
	List<String> list = new ArrayList<String>();
	public SimpleList() {
	}
	@XmlElement()
	public List<String> getList() {
		return list;
	}
	public void setList(List<String> list) {
		this.list = list;
	}
	
}
