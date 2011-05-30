package no.bimconverter.ifc.jaxb;

import java.util.List;

import javax.xml.bind.annotation.XmlElement;
import javax.xml.bind.annotation.XmlElementWrapper;
import javax.xml.bind.annotation.XmlRootElement;

@XmlRootElement
public class ZoneAssignment extends Assignment{
	private List<String> spaces;
	private List<String> subZones;
	
	public ZoneAssignment() {
	}
	
	public void addSpaceId(String spaceId) {
		this.spaces = this.checkAndInitializeArrayList(this.spaces);
		this.spaces.add(spaceId);
	}
	
	public void addZoneId(String id) {
		this.subZones = this.checkAndInitializeArrayList(this.subZones);
		this.subZones.add(id);
	}
	
	
	
	@XmlElementWrapper(name="spaces")
	@XmlElement(name="guid")
	public List<String> getSpaces() {
		return spaces;
	}

	public void setSpaces(List<String> spaces) {
		this.spaces = spaces;
	}
	@XmlElementWrapper(name="subZones")
	@XmlElement(name="guid")
	public List<String> getSubZones() {
		return subZones;
	}

	public void setSubZones(List<String> subZones) {
		this.subZones = subZones;
	}

}
