package no.bimfm.ifc.v2x3.owner;

import javax.xml.bind.annotation.XmlRootElement;

import jsdai.SIfc2x3.EIfcchangeactionenum;
import jsdai.SIfc2x3.EIfcownerhistory;
import jsdai.SIfc2x3.EIfcstateenum;
import jsdai.lang.SdaiException;
@XmlRootElement
public class OwnerHistory {
	
	private PersonAndOrganization owningUser;
	private Application owningApplication;
	private String state;
	private String changeAction;
	private String lastModifiedDate;
	private PersonAndOrganization lastModifyingUser;
	private Application lastModifyingApplication;
	private String CreationDate;
	
	public OwnerHistory() {
	}
	public OwnerHistory loadOwnerHistory(EIfcownerhistory ownerHistory) {
		try {
			this.owningUser = new PersonAndOrganization().load(ownerHistory.getOwninguser(null));
			this.owningApplication = new Application().load(ownerHistory.getOwningapplication(null));
			if(ownerHistory.testState(null)) {
				this.state = EIfcstateenum.toString(ownerHistory.getState(null));
			}
			if(ownerHistory.testChangeaction(null)) {
				this.changeAction = EIfcchangeactionenum.toString(ownerHistory.getChangeaction(null));
			}
			if(ownerHistory.testLastmodifieddate(null)) {
				this.lastModifiedDate = String.valueOf(ownerHistory.getLastmodifieddate(null));
			}
			if(ownerHistory.testLastmodifyinguser(null)) {
				this.lastModifyingUser = new PersonAndOrganization().load(ownerHistory.getLastmodifyinguser(null));
			}
			if(ownerHistory.testLastmodifyingapplication(null)) {
				this.lastModifyingApplication = new Application().load(ownerHistory.getLastmodifyingapplication(null));
			}
			if(ownerHistory.testCreationdate(null)) {
				this.CreationDate = String.valueOf(ownerHistory.getCreationdate(null));
			}
			
		} catch (SdaiException e) {
			e.printStackTrace();
		}
		return this;
	}
	public PersonAndOrganization getOwningUser() {
		return owningUser;
	}
	public void setOwningUser(PersonAndOrganization owningUser) {
		this.owningUser = owningUser;
	}
	public Application getOwningApplication() {
		return owningApplication;
	}
	public void setOwningApplication(Application owningApplication) {
		this.owningApplication = owningApplication;
	}
	public String getState() {
		return state;
	}
	public void setState(String state) {
		this.state = state;
	}
	public String getChangeAction() {
		return changeAction;
	}
	public void setChangeAction(String changeAction) {
		this.changeAction = changeAction;
	}
	public String getLastModifiedDate() {
		return lastModifiedDate;
	}
	public void setLastModifiedDate(String lastModifiedDate) {
		this.lastModifiedDate = lastModifiedDate;
	}
	public PersonAndOrganization getLastModifyingUser() {
		return lastModifyingUser;
	}
	public void setLastModifyingUser(PersonAndOrganization lastModifyingUser) {
		this.lastModifyingUser = lastModifyingUser;
	}
	public Application getLastModifyingApplication() {
		return lastModifyingApplication;
	}
	public void setLastModifyingApplication(Application lastModifyingApplication) {
		this.lastModifyingApplication = lastModifyingApplication;
	}
	public String getCreationDate() {
		return CreationDate;
	}
	public void setCreationDate(String creationDate) {
		CreationDate = creationDate;
	}
	
	

}
