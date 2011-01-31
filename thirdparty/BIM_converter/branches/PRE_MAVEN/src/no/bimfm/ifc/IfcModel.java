package no.bimfm.ifc;

public interface IfcModel extends Repositories {
	public int size();
	public void setIfcRepositoryName(String ifcRepositoryName);
	public String getIfcRepositoryName();
}
