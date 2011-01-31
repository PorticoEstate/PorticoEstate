package no.bimconverter.ifc;

public class IfcSdaiException extends RuntimeException {
	
	private static final long serialVersionUID = -4560953593088726863L;

	public IfcSdaiException(String message){
		super(message);
	}
	public IfcSdaiException(String message, Throwable t){
		super(message, t);
	}
	
}
