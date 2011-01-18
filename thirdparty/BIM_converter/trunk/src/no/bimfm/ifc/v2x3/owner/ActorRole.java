package no.bimfm.ifc.v2x3.owner;

import javax.xml.bind.annotation.XmlRootElement;

import jsdai.SIfc2x3.EIfcactorrole;
import jsdai.SIfc2x3.EIfcroleenum;
import jsdai.lang.SdaiException;
@XmlRootElement
public class ActorRole {
	private String role;
	private String userDefinedRole;
	private String description;
	public ActorRole() {
	}
	
	public ActorRole load(EIfcactorrole actorRole) {
		try {
			int roleId = actorRole.getRole(null);
			this.role = EIfcroleenum.toString(roleId);
			if(actorRole.testUserdefinedrole(null)) {
				this.userDefinedRole = actorRole.getUserdefinedrole(null);
			}
			if(actorRole.testDescription(null)) {
				this.description = actorRole.getDescription(null);
			}
		} catch (SdaiException e) {
			e.printStackTrace();
		}
		return this;
		
	}

	public String getRole() {
		return role;
	}

	public String getUserDefinedRole() {
		return userDefinedRole;
	}

	public String getDescription() {
		return description;
	}

	public void setRole(String role) {
		this.role = role;
	}

	public void setUserDefinedRole(String userDefinedRole) {
		this.userDefinedRole = userDefinedRole;
	}

	public void setDescription(String description) {
		this.description = description;
	}

}
