defmodule Pickems.Repo.Migrations.CreateUser do
  use Ecto.Migration

  def change do
    create table(:users) do
      add :name, :string
      add :email, :string
      add :password_hash, :string
      add :admin, :boolean

      timestamps
    end

    # Unique email address constraint, via DB index
    create index(:users, [:email], unique: true)
  end
end
