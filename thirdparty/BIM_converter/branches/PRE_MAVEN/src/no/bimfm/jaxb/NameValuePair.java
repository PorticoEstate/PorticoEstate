package no.bimfm.jaxb;

import javax.xml.bind.annotation.XmlRootElement;

@XmlRootElement
public class NameValuePair {
	public String name;
	public String value;
	public NameValuePair() {
	}
	public NameValuePair(String name, String value) {
		this.name = name;
		this.value = value;
	}
	
	@Override
	public String toString() {
		StringBuilder output = new StringBuilder();
		output.append("Name:\t"+name+"\n");
		output.append("Value:\t"+value+"\n");
		return output.toString();
	}
}
