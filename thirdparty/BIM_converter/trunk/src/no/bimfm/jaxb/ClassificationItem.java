package no.bimfm.jaxb;

import javax.xml.bind.annotation.XmlRootElement;

@XmlRootElement
public class ClassificationItem {
	
	public String itemKey;
	public String itemName;
	public String systemName;
	public String systemEdition;
	public ClassificationItem() {
	}
	public ClassificationItem(String itemKey, String itemName, String systemName, String systemEdition) {
		this.itemKey = itemKey;
		this.itemName = itemName;
		this.systemName = systemName;
		this.systemEdition = systemEdition;
	}
	
	
	
}
