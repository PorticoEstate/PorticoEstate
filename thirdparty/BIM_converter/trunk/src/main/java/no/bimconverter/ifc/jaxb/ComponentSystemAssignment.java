package no.bimconverter.ifc.jaxb;

import java.util.List;

import javax.xml.bind.annotation.XmlElement;
import javax.xml.bind.annotation.XmlElementWrapper;
import javax.xml.bind.annotation.XmlRootElement;

@XmlRootElement(name="component-system-assignment")
public class ComponentSystemAssignment extends Assignment {
	private List<String> components;
	private List<String> subSystems;
	public ComponentSystemAssignment() {
	}
	public void addComponentGuid(String guid) {
		this.components = super.checkAndInitializeArrayList(this.components);
		this.components.add(guid);
	}
	public void addSubSystemId(String guid) {
		this.subSystems = super.checkAndInitializeArrayList(this.subSystems);
		this.subSystems.add(guid);
	}
	@XmlElementWrapper(name="components")
	@XmlElement(name="guid")
	public List<String> getComponents() {
		return components;
	}
	public void setComponents(List<String> components) {
		this.components = components;
	}
	@XmlElementWrapper(name="sub-systems")
	@XmlElement(name="guid")
	public List<String> getSubSystems() {
		return subSystems;
	}
	public void setSubSystems(List<String> subSystems) {
		this.subSystems = subSystems;
	}
	
}
