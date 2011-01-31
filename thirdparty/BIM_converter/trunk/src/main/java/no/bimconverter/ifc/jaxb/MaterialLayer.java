package no.bimconverter.ifc.jaxb;

import javax.xml.bind.annotation.XmlRootElement;

@XmlRootElement
public class MaterialLayer {
	public String name;
	public String thickness;
	public String ventilated;
	public MaterialLayer() {
		super();
	}public MaterialLayer(String name) {
		super();
		this.name = name;
	}
	public MaterialLayer(String name, String thickness, String ventilated) {
		super();
		this.name = name;
		this.thickness = thickness;
		this.ventilated = ventilated;
	}
	
}
