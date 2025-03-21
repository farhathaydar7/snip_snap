class UserModel {
  constructor(data = {}) {
    this.id = data.id || null;
    this.username = data.username || "";
    this.name = data.name || "";
    this.email = data.email || "";
    this.createdAt = data.created_at ? new Date(data.created_at) : null;
    this.updatedAt = data.updated_at ? new Date(data.updated_at) : null;
  }

  static fromJson(json) {
    return new UserModel(json);
  }

  toJson() {
    return {
      id: this.id,
      username: this.username,
      name: this.name,
      email: this.email,
    };
  }
}

export default UserModel;
