package no.bimconverter.ifc.jaxb;

public class SpatialContainerItem {
	public String guid;
	
	public String type;
	public String name;
	public String description;
	public SpatialContainerItem() {
	}
	public SpatialContainerItem(String guid, String type, String name, String description) {
		super();
		this.guid = guid;
		this.type = type;
		this.name = name;
		this.description = description;
	}

}
