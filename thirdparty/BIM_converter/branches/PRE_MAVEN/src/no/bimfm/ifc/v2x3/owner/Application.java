package no.bimfm.ifc.v2x3.owner;

import javax.xml.bind.annotation.XmlRootElement;

import jsdai.SIfc2x3.EIfcapplication;
import jsdai.lang.SdaiException;
@XmlRootElement
public class Application {
	
	private Organization applicationDeveloper;
	private String version;
	private String applicationFullName;
	private String applicationIdentifier;
	
	public Application() {
	}
	
	public Application load(EIfcapplication application) {
		try {
			if(application.testApplicationdeveloper(null)) {
				this.applicationDeveloper = new Organization().load(application.getApplicationdeveloper(null));
			}
			if(application.testVersion(null)) {
				this.version = application.getVersion(null);
			}
			if(application.testApplicationfullname(null)) {
				this.applicationFullName = application.getApplicationfullname(null);
			}
			if(application.testApplicationidentifier(null)) {
				this.applicationIdentifier = application.getApplicationidentifier(null);
			}
		} catch (SdaiException e) {
			e.printStackTrace();
		}
		return this;
	}

	public Organization getApplicationDeveloper() {
		return applicationDeveloper;
	}

	public String getVersion() {
		return version;
	}

	public String getApplicationFullName() {
		return applicationFullName;
	}

	public String getApplicationIdentifier() {
		return applicationIdentifier;
	}

	public void setApplicationDeveloper(Organization applicationDeveloper) {
		this.applicationDeveloper = applicationDeveloper;
	}

	public void setVersion(String version) {
		this.version = version;
	}

	public void setApplicationFullName(String applicationFullName) {
		this.applicationFullName = applicationFullName;
	}

	public void setApplicationIdentifier(String applicationIdentifier) {
		this.applicationIdentifier = applicationIdentifier;
	}

}
