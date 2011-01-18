package no.bimfm.jaxb;

import javax.xml.bind.annotation.XmlRootElement;

@XmlRootElement
public class Attributes {
	private String guid;
	private String name;
	private String longName;
	private String description;
	private String phase; // for projects
	private String longtitude; // site
	private String latitude; // site
	private String elevation; // site, buildingstorey
	private String internalExternal; // space
	private String predefinedCoveringType; // covering
	private String constructionType; // windowStyle, doorstyle
	private String operationType; //windowStyle, doorstyle
	private String panelOperationType; // windowstyle
	

	public Attributes() {
	}
	
	
	/*
	 * @Override
	public boolean equals(Object o) {
		if ((o instanceof Attributes) 
				&& (((Attributes)o).getGuid() == this.guid)
				&& (((Attributes)o).getName() == this.name)
				&& (((Attributes)o).getLongName() == this.longName)
				&& (((Attributes)o).getDescription() == this.description)
				&& (((Attributes)o).getPhase() == this.phase)
				&& (((Attributes)o).getLongtitude() == this.longtitude)
				&& (((Attributes)o).getLatitude() == this.latitude)
				&& (((Attributes)o).getElevation() == this.elevation)
				&& (((Attributes)o).getInternalExternal() == this.internalExternal)
				&& (((Attributes)o).getPredefinedCoveringType() == this.predefinedCoveringType)) {
				return true;
				} else {
				return false;
				}
	}
	 */

	@Override
	public String toString() {
		return "Attributes [guid=" + guid + ", name=" + name + ", longName="
				+ longName + ", description=" + description + ", phase="
				+ phase + ", longtitude=" + longtitude + ", latitude="
				+ latitude + ", elevation=" + elevation + ", internalExternal="
				+ internalExternal + ", predefinedCoveringType="
				+ predefinedCoveringType + ", constructionType="
				+ constructionType + ", operationType=" + operationType
				+ ", panelOperationType=" + panelOperationType + "]";
	}


	@Override
	public int hashCode() {
		final int prime = 31;
		int result = 1;
		result = prime
				* result
				+ ((constructionType == null) ? 0 : constructionType.hashCode());
		result = prime * result
				+ ((description == null) ? 0 : description.hashCode());
		result = prime * result
				+ ((elevation == null) ? 0 : elevation.hashCode());
		result = prime * result + ((guid == null) ? 0 : guid.hashCode());
		result = prime
				* result
				+ ((internalExternal == null) ? 0 : internalExternal.hashCode());
		result = prime * result
				+ ((latitude == null) ? 0 : latitude.hashCode());
		result = prime * result
				+ ((longName == null) ? 0 : longName.hashCode());
		result = prime * result
				+ ((longtitude == null) ? 0 : longtitude.hashCode());
		result = prime * result + ((name == null) ? 0 : name.hashCode());
		result = prime * result
				+ ((operationType == null) ? 0 : operationType.hashCode());
		result = prime
				* result
				+ ((panelOperationType == null) ? 0 : panelOperationType
						.hashCode());
		result = prime * result + ((phase == null) ? 0 : phase.hashCode());
		result = prime
				* result
				+ ((predefinedCoveringType == null) ? 0
						: predefinedCoveringType.hashCode());
		return result;
	}


	@Override
	public boolean equals(Object obj) {
		if (this == obj)
			return true;
		if (obj == null)
			return false;
		if (getClass() != obj.getClass())
			return false;
		Attributes other = (Attributes) obj;
		if (constructionType == null) {
			if (other.constructionType != null)
				return false;
		} else if (!constructionType.equals(other.constructionType))
			return false;
		if (description == null) {
			if (other.description != null)
				return false;
		} else if (!description.equals(other.description))
			return false;
		if (elevation == null) {
			if (other.elevation != null)
				return false;
		} else if (!elevation.equals(other.elevation))
			return false;
		if (guid == null) {
			if (other.guid != null)
				return false;
		} else if (!guid.equals(other.guid))
			return false;
		if (internalExternal == null) {
			if (other.internalExternal != null)
				return false;
		} else if (!internalExternal.equals(other.internalExternal))
			return false;
		if (latitude == null) {
			if (other.latitude != null)
				return false;
		} else if (!latitude.equals(other.latitude))
			return false;
		if (longName == null) {
			if (other.longName != null)
				return false;
		} else if (!longName.equals(other.longName))
			return false;
		if (longtitude == null) {
			if (other.longtitude != null)
				return false;
		} else if (!longtitude.equals(other.longtitude))
			return false;
		if (name == null) {
			if (other.name != null)
				return false;
		} else if (!name.equals(other.name))
			return false;
		if (operationType == null) {
			if (other.operationType != null)
				return false;
		} else if (!operationType.equals(other.operationType))
			return false;
		if (panelOperationType == null) {
			if (other.panelOperationType != null)
				return false;
		} else if (!panelOperationType.equals(other.panelOperationType))
			return false;
		if (phase == null) {
			if (other.phase != null)
				return false;
		} else if (!phase.equals(other.phase))
			return false;
		if (predefinedCoveringType == null) {
			if (other.predefinedCoveringType != null)
				return false;
		} else if (!predefinedCoveringType.equals(other.predefinedCoveringType))
			return false;
		return true;
	}


	public String getGuid() {
		return guid;
	}

	public void setGuid(String guid) {
		this.guid = guid;
	}

	public String getName() {
		return name;
	}

	public void setName(String name) {
		this.name = name;
	}

	public String getLongName() {
		return longName;
	}

	public void setLongName(String longName) {
		this.longName = longName;
	}

	public String getDescription() {
		return description;
	}

	public void setDescription(String description) {
		this.description = description;
	}

	public String getPhase() {
		return phase;
	}

	public void setPhase(String phase) {
		this.phase = phase;
	}

	public String getLongtitude() {
		return longtitude;
	}

	public void setLongtitude(String longtitude) {
		this.longtitude = longtitude;
	}

	public String getLatitude() {
		return latitude;
	}

	public void setLatitude(String latitude) {
		this.latitude = latitude;
	}

	public String getElevation() {
		return elevation;
	}

	public void setElevation(String elevation) {
		this.elevation = elevation;
	}

	public String getInternalExternal() {
		return internalExternal;
	}

	public void setInternalExternal(String internalExternal) {
		this.internalExternal = internalExternal;
	}

	public String getPredefinedCoveringType() {
		return predefinedCoveringType;
	}

	public void setPredefinedCoveringType(String predefinedCoveringType) {
		this.predefinedCoveringType = predefinedCoveringType;
	}

	public String getConstructionType() {
		return constructionType;
	}

	public void setConstructionType(String constructionType) {
		this.constructionType = constructionType;
	}

	public String getOperationType() {
		return operationType;
	}

	public void setOperationType(String operationType) {
		this.operationType = operationType;
	}

	public String getPanelOperationType() {
		return panelOperationType;
	}

	public void setPanelOperationType(String panelOperationType) {
		this.panelOperationType = panelOperationType;
	}
	
}
