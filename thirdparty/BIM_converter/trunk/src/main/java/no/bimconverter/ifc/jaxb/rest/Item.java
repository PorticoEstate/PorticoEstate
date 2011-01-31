package no.bimconverter.ifc.jaxb.rest;

import javax.xml.bind.annotation.XmlRootElement;
import javax.xml.bind.annotation.XmlValue;


@XmlRootElement
public class Item {
	private String item;
	public Item() {
	}
	public Item(String item) {
		super();
		this.item = item;
	}
	@XmlValue
	public String getItem() {
		return item;
	}
	public void setItem(String item) {
		this.item = item;
	}
	

}
