package no.bimconverter.ifc.v2x3;

import java.util.List;

import javax.xml.bind.annotation.XmlElement;
import javax.xml.bind.annotation.XmlElementWrapper;
import javax.xml.bind.annotation.XmlRootElement;

import no.bimconverter.ifc.v2x3.object.Building;
import no.bimconverter.ifc.v2x3.object.BuildingStorey;
import no.bimconverter.ifc.v2x3.object.Project;
import no.bimconverter.ifc.v2x3.object.Site;
import no.bimconverter.ifc.v2x3.object.Space;
import no.bimconverter.ifc.v2x3.object.Zone;
import no.bimconverter.ifc.v2x3.object.element.BuildingServiceElement;
import no.bimconverter.ifc.v2x3.object.element.Covering;
import no.bimconverter.ifc.v2x3.object.element.Door;
import no.bimconverter.ifc.v2x3.object.element.Furnishing;
import no.bimconverter.ifc.v2x3.object.element.Window;



@XmlRootElement
public class WholeModelOutput {
	private ModelInformation modelInformation = new ModelInformation();
	private Project project = new Project();
	private Site site = new Site();
	private List<Building> buildings = null;
	private List<BuildingStorey> buildingStoreys = null;
	private List<Space> spaces = null;
	private List<Covering> covering = null;
	private List<Window> windows = null;
	private List<Door> doors = null;
	private List<Furnishing> furnishingElements = null;
	private List<Zone> zones = null;
	private List<BuildingServiceElement> buildingServiceElements = null;
	
	public WholeModelOutput() {
	}
	public void load(IfcModelImpl model) {
		this.modelInformation = model.getExchangeFileProperties();
		this.project = model.getProject();
		this.site = model.getSite();
		this.buildings = model.getBuildings();
		this.buildingStoreys = model.getBuildingStoreys();
		this.spaces = model.getSpaces();
		this.covering = model.getCoverings();
		this.windows = model.getWindows();
		this.doors = model.getDoors();
		this.furnishingElements = model.getFurnishing();
		this.zones = model.getZones();
		this.buildingServiceElements = model.getBuildingServiceElement();
		
	}
	public ModelInformation getModelInformation() {
		return modelInformation;
	}
	public void setModelInformation(ModelInformation modelInformation) {
		this.modelInformation = modelInformation;
	}
	public Project getProject() {
		return project;
	}
	public void setProject(Project project) {
		this.project = project;
	}
	public Site getSite() {
		return site;
	}
	public void setSite(Site site) {
		this.site = site;
	}
	@XmlElementWrapper(name="buildings")
	@XmlElement(name="building") 
	public List<Building> getBuildings() {
		return buildings;
	}
	public void setBuildings(List<Building> buildings) {
		this.buildings = buildings;
	}
	@XmlElementWrapper(name="buildingStoreys")
	@XmlElement(name="buildingStorey") 
	public List<BuildingStorey> getBuildingStoreys() {
		return buildingStoreys;
	}
	public void setBuildingStoreys(List<BuildingStorey> buildingStoreys) {
		this.buildingStoreys = buildingStoreys;
	}
	@XmlElementWrapper(name="spaces")
	@XmlElement(name="space") 
	public List<Space> getSpaces() {
		return spaces;
	}
	public void setSpaces(List<Space> spaces) {
		this.spaces = spaces;
	}
	@XmlElementWrapper(name="coverings")
	@XmlElement(name="covering") 
	public List<Covering> getCovering() {
		return covering;
	}
	public void setCovering(List<Covering> covering) {
		this.covering = covering;
	}
	@XmlElementWrapper(name="windows")
	@XmlElement(name="window") 
	public List<Window> getWindows() {
		return windows;
	}
	
	public void setWindows(List<Window> windows) {
		this.windows = windows;
	}
	@XmlElementWrapper(name="doors")
	@XmlElement(name="door") 
	public List<Door> getDoors() {
		return doors;
	}
	public void setDoors(List<Door> doors) {
		this.doors = doors;
	}
	@XmlElementWrapper(name="furnishingElements")
	@XmlElement(name="furnishingElement") 
	public List<Furnishing> getFurnishingElements() {
		return furnishingElements;
	}
	public void setFurnishingElements(List<Furnishing> furnishingElements) {
		this.furnishingElements = furnishingElements;
	}
	@XmlElementWrapper(name="zones")
	@XmlElement(name="zone") 
	public List<Zone> getZones() {
		return zones;
	}
	public void setZones(List<Zone> zones) {
		this.zones = zones;
	}
	@XmlElementWrapper(name="buildingServiceElements")
	@XmlElement(name="buildingServiceElement") 
	public List<BuildingServiceElement> getBuildingServiceElements() {
		return buildingServiceElements;
	}
	public void setBuildingServiceElements(
			List<BuildingServiceElement> buildingServiceElements) {
		this.buildingServiceElements = buildingServiceElements;
	}

}
