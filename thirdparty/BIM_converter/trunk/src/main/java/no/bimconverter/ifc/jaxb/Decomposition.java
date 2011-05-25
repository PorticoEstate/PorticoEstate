package no.bimconverter.ifc.jaxb;

import java.util.ArrayList;
import java.util.List;

import javax.xml.bind.annotation.XmlElement;
import javax.xml.bind.annotation.XmlElementWrapper;
import javax.xml.bind.annotation.XmlRootElement;

@XmlRootElement
public class Decomposition {
	private String site;
	private String project;
	private List<String> buildings;
	private List<String> buildingStoreys;
	private List<String> spaces;
	public Decomposition() {
	}
	
	public void addBuildingId(String buildingId) {
		this.buildings = this.checkAndInitializeArrayList(this.buildings);
		this.buildings.add(buildingId);
	}
	public void addBuildingStoreyId(String buildingStoreyId) {
		this.buildingStoreys = this.checkAndInitializeArrayList(this.buildingStoreys);
		this.buildingStoreys.add(buildingStoreyId);
	}
	public void addSpaceId(String spaceId) {
		this.spaces = this.checkAndInitializeArrayList(this.spaces);
		this.spaces.add(spaceId);
	}
	@XmlElementWrapper(name="buildingStoreys")
	@XmlElement(name="guid")
	public List<String> getBuildingStoreys() {
		return buildingStoreys;
	}

	public void setBuildingStoreys(List<String> buildingStoreys) {
		this.buildingStoreys = buildingStoreys;
	}

	private List<String> checkAndInitializeArrayList(List<String> list) {
		if(list == null) {
			return new ArrayList<String>();
		} else {
			return list;
		}
	}
	public String getSite() {
		return site;
	}
	public void setSite(String site) {
		this.site = site;
	}
	@XmlElementWrapper(name="buildings")
	@XmlElement(name="guid")
	public List<String> getBuildings() {
		return buildings;
	}
	public void setBuildings(List<String> buildings) {
		this.buildings = buildings;
	}
	
	@Override
	public String toString() {
		StringBuilder sb = new StringBuilder();
		sb.append("Project:\t"+this.project+"\n");
		sb.append("site:\t"+String.valueOf(this.site)+"\n");
		if(this.buildings != null) {
			sb.append("Buildings:\n");
			for(String buildingId : this.buildings) {
				sb.append("\t"+buildingId+"\n");
			}
		}
		if(this.buildingStoreys != null) {
			sb.append("Building Stories:\n");
			for(String buildingStoryId : this.buildingStoreys) {
				sb.append("\t"+buildingStoryId+"\n");
			}
		}
		if(this.spaces != null) {
			sb.append("Spaces:\n");
			for(String spaceId : this.spaces) {
				sb.append("\t"+spaceId+"\n");
			}
		}
		
		return sb.toString();
	}

	public String getProject() {
		return project;
	}

	public void setProject(String project) {
		this.project = project;
	}
	@XmlElementWrapper(name="spaces")
	@XmlElement(name="guid")
	public List<String> getSpaces() {
		return spaces;
	}

	public void setSpaces(List<String> spaces) {
		this.spaces = spaces;
	}

}
