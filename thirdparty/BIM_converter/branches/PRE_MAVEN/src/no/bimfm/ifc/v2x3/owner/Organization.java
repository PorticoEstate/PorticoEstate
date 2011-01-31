package no.bimfm.ifc.v2x3.owner;

import javax.xml.bind.annotation.XmlRootElement;

import jsdai.SIfc2x3.EIfcorganization;
import jsdai.lang.SdaiException;

/*
 * IfcOrganization, ifc version 2x3
 */
@XmlRootElement
public class Organization  extends PersonAndOrganization{
	private String id;
	private String name;
	private String description;
	private  ActorRole[] roles;
	private  Address[] addresses;
	
	public Organization() {
	}
	
	public Organization load(EIfcorganization organization) {
		try {
			if(organization.testId(null)) {
				this.id = organization.getId(null);
			}
			if(organization.testName(null)) {
				this.name = organization.getName(null);
			}
			if(organization.testDescription(null)) {
				this.description = organization.getDescription(null);
			}
			if(organization.testRoles(null)) {
				this.roles = getRoleArray(organization.getRoles(null));
			}
		} catch (SdaiException e) {
			e.printStackTrace();
		}
		return this;
	}

	public String getId() {
		return id;
	}

	public String getName() {
		return name;
	}

	public String getDescription() {
		return description;
	}

	public ActorRole[] getRoles() {
		return roles;
	}

	public Address[] getAddresses() {
		return addresses;
	}

	public void setId(String id) {
		this.id = id;
	}

	public void setName(String name) {
		this.name = name;
	}

	public void setDescription(String description) {
		this.description = description;
	}

	public void setRoles(ActorRole[] roles) {
		this.roles = roles;
	}

	public void setAddresses(Address[] addresses) {
		this.addresses = addresses;
	}
	
}
