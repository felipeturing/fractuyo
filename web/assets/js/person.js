var Identification = function() {
	var number, type

	/**
	 * Set document number according type
	 * @var t integer type according catalog 06
	 */
	this.setIdentity = function(n, t) {
		if(validateDocumentNumber(parseInt(t, 16), n)) {
			number = n
			type = t
			return this
		}
		throw new Error("Número de identificación inconsistente.")
	}

	this.getNumber = function() {
		return number
	}

	this.getType = function() {
		return type
	}
}

var Person = function() {
	var name, identification
	var address

	this.getName = function(withCdata = false) {
		return withCdata ? `<![CDATA[${name}]]>` : name
	}

	this.setName = function(n) {
		if(n.length > 0) {
			name = n
		}
	}

	this.setIdentification = function(i) {
		identification = i
	}

	this.getIdentification = function() {
		return identification
	}

	this.setAddress = function(a) {
		address = a
	}

	this.getAddress = function(withCdata = false) {
		return withCdata ? `<![CDATA[${address}]]>` : address
	}

	this.isNatural = function() {
		return isNatural
	}
}

var Taxpayer = function() {
	var cert, key
	var solUser, solPass

	this.setCert = function(c) {
		cert = c
	}

	this.getCert = function() {
		return cert
	}

	this.getKey = function() {
		return key
	}

	this.getKey = function() {
		return key
	}

	this.setKey = function(k) {
		key = k
	}

	this.setSolUser = function(su) {
		solUser = su
	}

	this.setSolPass = function(sp) {
		solPass = sp
	}

	this.clearData = function() {
		name = identity = cert = key = null
	}
}

Taxpayer.prototype = new Person()
