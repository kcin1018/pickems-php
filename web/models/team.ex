defmodule Pickems.Team do
  use Pickems.Web, :model

  schema "teams" do
    field :name, :string
    field :slug, :string
    field :paid, :boolean

    timestamps
  end

  @required_fields ~w(name paid)
  @optional_fields ~w()

  @doc """
  Creates a changeset based on the `model` and `params`.

  If no params are provided, an invalid changeset is returned
  with no validation performed.
  """
  def changeset(model, params \\ :empty) do
    model
    |> cast(params, @required_fields, @optional_fields)
    |> generate_slug
    |> unique_constraint(:name)
  end

  defp generate_slug(%{valid?: false} = changeset), do: changeset
  defp generate_slug(%{valid?: true} = changeset) do
    slug = Slugger.slugify_downcase(Ecto.Changeset.get_field(changeset, :name))
    Ecto.Changeset.put_change(changeset, :slug, slug)
  end
end
