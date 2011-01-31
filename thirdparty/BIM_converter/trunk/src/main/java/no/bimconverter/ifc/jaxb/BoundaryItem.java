package no.bimconverter.ifc.jaxb;

public class BoundaryItem {
	public String guid;
	public String type;
	public String physicalOrVirtualBoundary;
	public String internalOrExternalBoundary;
	public BoundaryItem() {
	}
	public BoundaryItem(String guid, String type,
			String physicalOrVirtualBoundary, String internalOrExternalBoundary) {
		super();
		this.guid = guid;
		this.type = type;
		this.physicalOrVirtualBoundary = physicalOrVirtualBoundary;
		this.internalOrExternalBoundary = internalOrExternalBoundary;
	}
	
	@Override
	public boolean equals(Object o) {
		if ((o instanceof BoundaryItem) 
				&& (((BoundaryItem)o).guid == this.guid)
				&& (((BoundaryItem)o).type == this.type)
				&& (((BoundaryItem)o).physicalOrVirtualBoundary == this.physicalOrVirtualBoundary)
				&& (((BoundaryItem)o).internalOrExternalBoundary == this.internalOrExternalBoundary)) {
				return true;
				} else {
				return false;
				}
	}
	@Override
	public String toString() {
		StringBuilder current = new StringBuilder();
		current.append("Guid:\t"+guid+"\n");
		current.append("Type:\t"+type+"\n");
		current.append("Physical or virtual:\t"+physicalOrVirtualBoundary+"\n");
		current.append("Internal or external:\t"+internalOrExternalBoundary+"\n");
		return current.toString();
	}
	
}
