package no.bimfm.jaxb;

import java.util.ArrayList;
import java.util.List;

import javax.xml.bind.annotation.XmlElement;
import javax.xml.bind.annotation.XmlElementWrapper;
import javax.xml.bind.annotation.XmlRootElement;
@XmlRootElement
public class SpaceSpatialContainer {
	public SpaceSpatialContainer() {
	}
	private List<SpatialContainerItem> elements;
	private List<SpatialContainerItem> coverings;
	private List<SpatialContainerItem> doorsWindows;
	
	private List<SpatialContainerItem> checkAndInitializeArrayList(List<SpatialContainerItem> list) {
		if(list == null) {
			return new ArrayList<SpatialContainerItem>();
		} else {
			return list;
		}
	}
	
	public void addCovering(SpatialContainerItem item) {
		this.coverings = checkAndInitializeArrayList(this.coverings);
		this.coverings.add(item);
	}
	public void addElement(SpatialContainerItem item) {
		this.elements = checkAndInitializeArrayList(this.elements);
		this.elements.add(item);
	}
	public void addDoorWindow(SpatialContainerItem item) {
		this.doorsWindows = checkAndInitializeArrayList(this.doorsWindows);
		this.doorsWindows.add(item);
	}
	@XmlElementWrapper(name="element")
	@XmlElement(name="item") 
	public List<SpatialContainerItem> getElements() {
		return elements;
	}

	public void setElements(List<SpatialContainerItem> elements) {
		this.elements = elements;
	}
	@XmlElementWrapper(name="covering")
	@XmlElement(name="item")
	public List<SpatialContainerItem> getCoverings() {
		return coverings;
	}

	public void setCoverings(List<SpatialContainerItem> coverings) {
		this.coverings = coverings;
	}
	@XmlElementWrapper(name="doorsWindows")
	@XmlElement(name="item")
	public List<SpatialContainerItem> getDoorsWindows() {
		return doorsWindows;
	}

	public void setDoorsWindows(List<SpatialContainerItem> doorsWindows) {
		this.doorsWindows = doorsWindows;
	}
}

